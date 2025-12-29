<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\User;
use App\Models\UserDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\MassAssignmentException;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;


class RegisteredUserController extends Controller
{
    /**
     * Known Bahamian islands used in validation / select lists.
     */
    private $bahamianIslands = [
        'New Providence', 'Grand Bahama', 'Abaco', 'Andros', 'Eleuthera',
        'Cat Island', 'Exuma', 'Long Island', 'San Salvador', 'Acklins',
        'Crooked Island', 'Mayaguana', 'Inagua', 'Rum Cay', 'Bimini'
    ];

    /**
     * Show the registration page and the current UI step.
     *
     * @return View
     */
    public function create(): View
    {
        $step = session('registration_step', 1);
        $step2 = session('registration.step2', null);

        $selectedPackage = null;
        if (!empty($step2['package_id'])) {
            try {
                $selectedPackage = Package::find($step2['package_id']);
            } catch (\Throwable $e) {
                Log::warning('Failed to load package for registration view: ' . $e->getMessage());
                $selectedPackage = null;
            }
        }

        return view('auth.register', [
            'islands' => $this->bahamianIslands,
            'step' => $step,
            'registration_step2' => $step2,
            'registration_package' => $selectedPackage,
        ]);
    }

    /**
     * Optional alias if you use a different route name.
     */
    public function showRegistrationForm()
    {
        return $this->create();
    }

    /**
     * Step 1: Basic account creation only (name, email, password, terms).
     * Creates user account immediately and redirects to Default Dashboard.
     */
    public function step1(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'email_confirmation' => 'required|same:email',
            'password' => ['required', 'confirmed', Password::min(8)],
            'agree_terms' => 'required|accepted',
        ], [
            'agree_terms.accepted' => 'You must agree to CayMark\'s Terms and Privacy Policy to create an account.',
        ]);

        DB::beginTransaction();

        try {
            // Create user account immediately
            $user = User::create([
                'name' => trim($validated['first_name'] . ' ' . $validated['last_name']),
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => null, // No role assigned yet
                'registration_complete' => false, // Registration not complete
            ]);

            DB::commit();

            // Log user in automatically
            Auth::login($user);

            // Send Email #1: Complete Your Registration
            $this->sendRegistrationStep1Email($user);

            // Redirect to Default Dashboard
            return redirect()->route('dashboard.default')
                ->with('success', 'Account created successfully! Please complete your registration to access all features.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Registration Step 1 failed: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->route('register')
                ->with('error', 'Account creation failed. Please try again.');
        }
    }

    /**
     * Step 2: role + package selection.
     * Stores package selection summary in session (role, package_id, price).
     */
    public function step2(Request $request)
    {
        $request->session()->put('registration_step', 2);

        $validated = $request->validate([
            'role' => 'required|in:buyer,seller',
            'package_id' => 'required|exists:packages,id',
        ]);

        $package = Package::findOrFail($validated['package_id']);

        if ($package->role !== $validated['role']) {
            return back()->withErrors(['package_id' => 'Selected package does not match the chosen role.']);
        }

        $request->session()->put('registration.step2', [
            'role' => $validated['role'],
            'package_id' => $package->id,
            'package_name' => $package->title ?? null,
            'price' => $package->price ?? 0,
        ]);
        

        // advance UI to step 3
        $request->session()->put('registration_step', 3);

        return redirect()->route('register');
    }

public function step3(Request $request)
{
    Log::debug('Registration step3 called', [
        'route' => Route::currentRouteName(),
        'session_keys' => array_keys($request->session()->all()),
        'has_step1' => $request->session()->has('registration.step1'),
        'has_step2' => $request->session()->has('registration.step2'),
        'incoming' => $request->only(['agree_terms', 'marketing_opt_in']),
    ]);

    // Must have previous steps in session
    if (!$request->session()->has('registration.step1') || !$request->session()->has('registration.step2')) {
        Log::warning('Missing step1 or step2 in session when reaching step3');
        return redirect()->route('register')->with('error', 'Please complete all registration steps.');
    }

    // mark UI step
    $request->session()->put('registration_step', 3);

    try {
        $validated = $request->validate([
            'agree_terms' => 'required|accepted',
            'marketing_opt_in' => 'sometimes|boolean',
        ]);
    } catch (ValidationException $ve) {
        Log::info('Validation failed on step3', ['errors' => $ve->validator->errors()->toArray()]);
        return redirect()->back()->withErrors($ve->validator)->withInput()->with('error', 'Please fix the highlighted errors.');
    }

    $step1Data = $request->session()->get('registration.step1', []);
    $step2Data = $request->session()->get('registration.step2', []);

    if (empty($step1Data) || empty($step2Data)) {
        Log::error('Empty step1/step2 after validation (unexpected).', compact('step1Data', 'step2Data'));
        return redirect()->route('register')->with('error', 'Missing registration data. Please start again.');
    }

    DB::beginTransaction();

    try {
        // Create the user (guarded by model $fillable)
        $userData = [
            'name' => trim(($step1Data['first_name'] ?? '') . ' ' . ($step1Data['last_name'] ?? '')),
            'email' => $step1Data['email'] ?? null,
            'password' => Hash::make($step1Data['password'] ?? Str::random(12)),
            'username' => $step1Data['username'] ?? null,
            'nationality' => $step1Data['nationality'] ?? null,
            'island' => $step1Data['island'] ?? null,
            'dob' => $step1Data['dob'] ?? null,
            'gender' => $step1Data['gender'] ?? null,
            'phone' => $step1Data['phone'] ?? null,
            'marketing_opt_in' => !empty($validated['marketing_opt_in']),
        ];

        $user = User::create($userData);

        // defensive check
        if (empty($step2Data['package_id'])) {
            throw new \Exception('Package id missing from session step2');
        }

        $package = Package::find($step2Data['package_id']);
        if (!$package) {
            throw new \Exception('Selected package not found (id=' . $step2Data['package_id'] . ')');
        }

        // create subscription
        $subscription = Subscription::create([
            'user_id' => $user->id,
            'package_id' => $package->id,
            'starts_at' => now(),
            'ends_at' => $package->duration_days ? now()->addDays($package->duration_days) : null,
            'status' => 'pending',
        ]);

        // create payment
        $payment = Payment::create([
            'user_id' => $user->id,
            'subscription_id' => $subscription->id,
            'amount' => $package->price,
            'method' => 'bank_wire',
            'status' => 'pending',
            'metadata' => [
                'bank_account' => 'MERCHANT_BANK_ACCOUNT_DETAILS',
                'reference' => 'REG-' . $user->id . '-' . time(),
                'instructions' => 'Wire transfer to merchant bank account',
            ],
        ]);

        // Move documents from temp to permanent public/uploads/user_documents/{user_id}/
        if ($request->session()->has('registration.documents')) {
            $docs = $request->session()->get('registration.documents', []);
            foreach ($docs as $idx => $doc) {
                try {
                    // temp path stored in session is relative to storage/app (as produced by ->store())
                    $tempRelative = $doc['temp_path'] ?? null;
                    if (empty($tempRelative)) {
                        Log::warning('Document missing temp_path in session entry; skipping', ['user_id' => $user->id, 'idx' => $idx, 'doc' => $doc]);
                        continue;
                    }

                    $sourceFull = storage_path('app/' . $tempRelative);
                    if (!File::exists($sourceFull)) {
                        Log::warning('Temp document file missing on disk; skipping', ['user_id' => $user->id, 'source' => $sourceFull]);
                        continue;
                    }

                    // ensure destination directory exists in public/uploads
                    $destDir = public_path('uploads/user_documents/' . $user->id);
                    if (!File::exists($destDir)) {
                        File::makeDirectory($destDir, 0755, true);
                    }

                    // unique filename: preserve original extension
                    $original = $doc['original_name'] ?? ('document_' . $idx);
                    $ext = pathinfo($original, PATHINFO_EXTENSION) ?: 'dat';
                    $newFileName = 'USER_DOC_' . microtime(true) . '_' . uniqid() . '.' . $ext;
                    $destFull = $destDir . DIRECTORY_SEPARATOR . $newFileName;

                    // move the file (equivalent to your listing image pattern)
                    if (!@rename($sourceFull, $destFull)) {
                        // fallback to File::move
                        File::move($sourceFull, $destFull);
                    }

                    // save UserDocument record (path relative to public/)
                    UserDocument::create([
                        'user_id' => $user->id,
                        // doc_type kept small and simple to avoid enum/data-truncation issues
                        'doc_type' => 'id' . $idx,
                        'path' => 'uploads/user_documents/' . $user->id . '/' . $newFileName,
                        'filename' => $original,
                        'mime_type' => $doc['mime_type'] ?? null,
                        'size' => $doc['size'] ?? null,
                        'verified' => false,
                    ]);
                } catch (\Exception $ex) {
                    // Log and continue: do not abort the whole registration for a single file
                    Log::warning('Failed to move/create user document', ['user_id' => $user->id, 'idx' => $idx, 'exception' => $ex->getMessage()]);
                }
            }
        } else {
            Log::info('No registration.documents found in session while finalizing registration', ['user_id' => $user->id]);
        }

        DB::commit();

        // After commit, double-check the user exists at the DB level and log it
        $exists = DB::table('users')->where('id', $user->id)->exists();
        Log::info('Registration successful', ['user_id' => $user->id, 'subscription_id' => $subscription->id, 'payment_id' => $payment->id, 'db_exists' => $exists]);

        // Clear registration session pieces
        $request->session()->forget(['registration.step1', 'registration.step2', 'registration.documents', 'registration_step']);

        // Redirect (PRG) instead of returning a view on POST so the URL is not left at /register/step3
        if (Route::has('register.complete')) {
            return redirect()->route('register.complete')->with('success', 'Registration complete. Please follow payment instructions.');
        }

        if (Route::has('welcome')) {
            return redirect()->route('welcome')->with('success', 'Registration complete. Please follow payment instructions.');
        }

        // fallback to homepage
        return redirect('/')->with('success', 'Registration complete. Please follow payment instructions.');
    } catch (QueryException $qe) {
        DB::rollBack();
        Log::error('DB QueryException during registration: ' . $qe->getMessage(), ['sql' => $qe->getSql(), 'bindings' => $qe->getBindings()]);
        return redirect()->route('register')->with('error', 'A database error occurred.');
    } catch (MassAssignmentException $ma) {
        DB::rollBack();
        Log::error('MassAssignmentException during registration: ' . $ma->getMessage());
        return redirect()->route('register')->with('error', 'Server error while creating user (mass assignment). Contact support.');
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Registration failed: ' . $e->getMessage(), ['exception' => $e]);
        return redirect()->route('register')->with('error', 'Registration failed. ' . $e->getMessage());
    }
}




    /**
     * Go back one UI step.
     */
    public function back(Request $request)
    {
        $currentStep = $request->session()->get('registration_step', 1);
        if ($currentStep > 1) {
            $request->session()->put('registration_step', $currentStep - 1);
        }
        return redirect()->route('register');
    }

    /**
     * Show membership selection page (for users who created basic account).
     */
    public function finishRegistration()
    {
        $user = Auth::user();
        
        // If registration is already complete, redirect to appropriate dashboard
        if ($user->isRegistrationComplete()) {
            return redirect()->route('dashboard');
        }

        // Get packages for display
        $buyerPackages = Package::forRole('buyer')->get();
        $sellerPackages = Package::forRole('seller')->get();

        return view('auth.finish-registration', [
            'buyerPackages' => $buyerPackages,
            'sellerPackages' => $sellerPackages,
        ]);
    }

    /**
     * Store membership selection.
     */
    public function storeMembership(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'role' => 'required|in:buyer,seller',
            'package_id' => 'required|exists:packages,id',
        ]);

        $package = Package::findOrFail($validated['package_id']);

        if ($package->role !== $validated['role']) {
            return back()->withErrors(['package_id' => 'Selected package does not match the chosen role.']);
        }

        // Store in session for Step 3
        $request->session()->put('finish_registration', [
            'role' => $validated['role'],
            'package_id' => $package->id,
            'package_name' => $package->title ?? null,
            'price' => $package->price ?? 0,
        ]);

        return redirect()->route('finish.registration.complete.show');
    }

    /**
     * Show Step 3: Verification & Payment page.
     */
    public function showCompleteRegistration()
    {
        $user = Auth::user();
        $finishData = session('finish_registration');

        if (!$finishData) {
            return redirect()->route('finish.registration')
                ->with('error', 'Please select your membership first.');
        }

        $package = Package::find($finishData['package_id']);

        return view('auth.complete-registration', [
            'user' => $user,
            'package' => $package,
            'finishData' => $finishData,
        ]);
    }

    /**
     * Complete registration: Handle document upload and payment.
     */
    public function completeRegistration(Request $request)
    {
        $user = Auth::user();
        $finishData = session('finish_registration');

        if (!$finishData) {
            return redirect()->route('finish.registration')
                ->with('error', 'Please select your membership first.');
        }

        $package = Package::findOrFail($finishData['package_id']);
        $isBusinessSeller = $finishData['role'] === 'seller' && $package->price > 0;

        // Validation rules
        $rules = [
            'id_document' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'id_type' => 'required|in:Passport,Driver License,National ID',
        ];

        // Business Seller requires additional fields
        if ($isBusinessSeller) {
            $rules['business_license'] = 'required|file|mimes:jpg,jpeg,png,pdf|max:5120';
            $rules['relationship_to_business'] = 'required|in:Owner,Founder,Shareholder,Employee,Authorized Representative,Manager';
        }

        // Payment required for Buyer and Business Seller
        if ($finishData['role'] === 'buyer' || $isBusinessSeller) {
            $rules['card_number'] = 'required|string';
            $rules['expiry_month'] = 'required|numeric|min:1|max:12';
            $rules['expiry_year'] = 'required|numeric|min:' . date('Y');
            $rules['cvc'] = 'required|string|size:3';
        }

        $validated = $request->validate($rules);

        DB::beginTransaction();

        try {
            // Update user with ID type
            $user->id_type = $validated['id_type'];
            if ($isBusinessSeller) {
                $user->relationship_to_business = $validated['relationship_to_business'];
            }

            // Handle ID document upload
            if ($request->hasFile('id_document')) {
                $idFile = $request->file('id_document');
                $idPath = $idFile->store('user_documents/' . $user->id, 'public');
                
                UserDocument::create([
                    'user_id' => $user->id,
                    'doc_type' => 'id',
                    'path' => $idPath,
                    'filename' => $idFile->getClientOriginalName(),
                    'mime_type' => $idFile->getMimeType(),
                    'size' => $idFile->getSize(),
                    'verified' => false,
                ]);
            }

            // Handle Business License upload
            if ($isBusinessSeller && $request->hasFile('business_license')) {
                $licenseFile = $request->file('business_license');
                $licensePath = $licenseFile->store('user_documents/' . $user->id, 'public');
                
                $user->business_license_path = $licensePath;
                
                UserDocument::create([
                    'user_id' => $user->id,
                    'doc_type' => 'business_license',
                    'path' => $licensePath,
                    'filename' => $licenseFile->getClientOriginalName(),
                    'mime_type' => $licenseFile->getMimeType(),
                    'size' => $licenseFile->getSize(),
                    'verified' => false,
                ]);
            }

            // Process payment if required
            if ($finishData['role'] === 'buyer' || $isBusinessSeller) {
                // TODO: Integrate with payment gateway (Stripe)
                // For now, create a pending payment record
                $subscription = Subscription::create([
                    'user_id' => $user->id,
                    'package_id' => $package->id,
                    'starts_at' => now(),
                    'ends_at' => $package->duration_days ? now()->addDays($package->duration_days) : null,
                    'status' => 'pending',
                ]);

                Payment::create([
                    'user_id' => $user->id,
                    'subscription_id' => $subscription->id,
                    'amount' => $package->price,
                    'method' => 'credit_card',
                    'status' => 'pending', // Will be updated after payment gateway confirmation
                    'metadata' => [
                        'card_last4' => substr($validated['card_number'], -4),
                    ],
                ]);
            } else {
                // Individual Seller - no payment required
                $subscription = Subscription::create([
                    'user_id' => $user->id,
                    'package_id' => $package->id,
                    'starts_at' => now(),
                    'ends_at' => null,
                    'status' => 'active',
                ]);
            }

            // Assign role and mark registration as complete
            $user->role = $finishData['role'];
            $user->registration_complete = true;
            $user->save();

            DB::commit();

            // Clear session
            $request->session()->forget('finish_registration');

            // Send Email #2: Registration Complete
            $this->sendRegistrationCompleteEmail($user);

            // Redirect to appropriate dashboard
            return redirect()->route('dashboard')
                ->with('success', 'Registration completed successfully! Welcome to CayMark.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Complete registration failed: ' . $e->getMessage(), ['exception' => $e]);
            return back()->with('error', 'Registration completion failed. Please try again.');
        }
    }

    /**
     * Send Email #1: Complete Your Registration
     */
    private function sendRegistrationStep1Email(User $user)
    {
        try {
            Mail::send('emails.registration-step1', ['user' => $user], function ($message) use ($user) {
                $message->to($user->email, $user->name)
                    ->subject('Complete Your Registration - CayMark');
            });
        } catch (\Exception $e) {
            Log::error('Failed to send registration step 1 email: ' . $e->getMessage());
        }
    }

    /**
     * Send Email #2: Welcome to CayMark - Registration Successful
     */
    private function sendRegistrationCompleteEmail(User $user)
    {
        try {
            Mail::send('emails.registration-complete', ['user' => $user], function ($message) use ($user) {
                $message->to($user->email, $user->name)
                    ->subject('Welcome to CayMark - Registration Successful');
            });
            
            // Send in-app notifications
            $notificationService = new \App\Services\NotificationService();
            $notificationService->registrationCompleted($user);
            $notificationService->welcomeToCayMark($user);
        } catch (\Exception $e) {
            Log::error('Failed to send registration complete email: ' . $e->getMessage());
        }
    }
}
