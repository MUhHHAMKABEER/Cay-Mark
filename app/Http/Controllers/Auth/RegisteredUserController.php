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
            'password' => ['required', 'confirmed', Password::min(8)],
            'agree_terms' => 'required|accepted',
        ], [
            'agree_terms.accepted' => 'You must agree to CayMark\'s Terms and Privacy Policy to create an account.',
        ]);

        DB::beginTransaction();

        try {
            Log::info('=== REGISTRATION STEP 1 START ===', [
                'email' => $validated['email'],
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
            ]);
            
            // Generate unique username from first name and last name
            $firstName = strtolower(trim($validated['first_name']));
            $lastName = strtolower(trim($validated['last_name']));
            
            Log::info('Step 1: Names cleaned', [
                'first_name_original' => $validated['first_name'],
                'last_name_original' => $validated['last_name'],
                'first_name_lowercase' => $firstName,
                'last_name_lowercase' => $lastName,
            ]);
            
            // Remove spaces and special characters, keep only alphanumeric
            $firstName = preg_replace('/[^a-z0-9]/', '', $firstName);
            $lastName = preg_replace('/[^a-z0-9]/', '', $lastName);
            
            Log::info('Step 2: After regex cleaning', [
                'first_name_cleaned' => $firstName,
                'last_name_cleaned' => $lastName,
                'first_name_empty' => empty($firstName),
                'last_name_empty' => empty($lastName),
            ]);
            
            // Fallback if names become empty after cleaning
            if (empty($firstName)) {
                $firstName = 'user';
                Log::warning('First name became empty after cleaning, using fallback: user');
            }
            if (empty($lastName)) {
                $lastName = 'name';
                Log::warning('Last name became empty after cleaning, using fallback: name');
            }
            
            // Combine first name and last name
            $baseUsername = $firstName . '.' . $lastName;
            $username = $baseUsername;
            $counter = 1;
            
            Log::info('Step 3: Base username generated', [
                'base_username' => $baseUsername,
                'initial_username' => $username,
            ]);
            
            // Check existing users count
            $existingUsersCount = User::count();
            Log::info('Step 4: Database check', [
                'total_users_in_db' => $existingUsersCount,
                'checking_username' => $username,
            ]);
            
            // Ensure username is unique (check before insert)
            $checkCount = 0;
            while (User::where('username', $username)->exists()) {
                $checkCount++;
                Log::warning('Username conflict found', [
                    'attempt' => $checkCount,
                    'conflicting_username' => $username,
                    'existing_user' => User::where('username', $username)->first(['id', 'email', 'name']),
                ]);
                
                $username = $baseUsername . $counter;
                $counter++;
                
                // Safety limit to prevent infinite loop
                if ($counter > 1000) {
                    // Fallback to email-based username if too many conflicts
                    $emailPart = strtolower(explode('@', $validated['email'])[0]);
                    $username = preg_replace('/[^a-z0-9]/', '', $emailPart) . '_' . time();
                    Log::error('Username generation exceeded limit, using email fallback', [
                        'final_username' => $username,
                    ]);
                    break;
                }
            }
            
            if ($checkCount > 0) {
                Log::info('Step 5: Username conflicts resolved', [
                    'conflicts_found' => $checkCount,
                    'final_username' => $username,
                ]);
            }
            
            // Final check before insert
            $finalCheck = User::where('username', $username)->exists();
            Log::info('Step 6: Final username check before insert', [
                'username' => $username,
                'username_exists' => $finalCheck,
                'all_existing_usernames' => User::pluck('username')->toArray(),
            ]);
            
            if ($finalCheck) {
                Log::error('CRITICAL: Username still exists after all checks!', [
                    'username' => $username,
                    'existing_user_details' => User::where('username', $username)->first(),
                ]);
                throw new \Exception('Username conflict detected after all resolution attempts. Username: ' . $username);
            }
            
            // Log for debugging
            Log::info('Step 7: Attempting user creation', [
                'name' => trim($validated['first_name'] . ' ' . $validated['last_name']),
                'email' => $validated['email'],
                'username' => $username,
                'first_name_cleaned' => $firstName,
                'last_name_cleaned' => $lastName,
            ]);
            
            // Create user account immediately
            $user = User::create([
                'name' => trim($validated['first_name'] . ' ' . $validated['last_name']),
                'email' => $validated['email'],
                'username' => $username,
                'password' => Hash::make($validated['password']),
                'role' => null, // No role assigned yet
                'registration_complete' => false, // Registration not complete
            ]);
            
            Log::info('Step 8: User::create() called', [
                'user_object' => $user ? 'created' : 'null',
                'user_id' => $user->id ?? 'no_id',
            ]);
            
            // Verify user was created
            if (!$user || !$user->id) {
                Log::error('User creation returned null or no ID', [
                    'user_object' => $user,
                    'user_id' => $user->id ?? 'missing',
                ]);
                throw new \Exception('User creation failed - no user ID returned');
            }
            
            // Verify user exists in database
            $createdUser = User::find($user->id);
            if (!$createdUser) {
                Log::error('User not found in database after creation', [
                    'user_id' => $user->id,
                    'username' => $username,
                ]);
                throw new \Exception('User was created but not found in database');
            }
            
            Log::info('=== REGISTRATION STEP 1 SUCCESS ===', [
                'user_id' => $user->id, 
                'username' => $username,
                'email' => $user->email,
                'name' => $user->name,
                'db_verified' => $createdUser !== null,
                'user_in_db' => User::where('id', $user->id)->exists(),
            ]);

            DB::commit();

            // Log user in automatically
            Auth::login($user);

            // Send Email #1: Complete Your Registration
            $this->sendRegistrationStep1Email($user);

            // Redirect to Default Dashboard
            return redirect()->route('dashboard.default')
                ->with('success', 'Account created successfully! Please complete your registration to access all features.');

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            
            $errorMessage = $e->getMessage();
            $sql = $e->getSql() ?? 'N/A';
            $bindings = $e->getBindings() ?? [];
            
            Log::error('=== REGISTRATION STEP 1 FAILED (Database QueryException) ===', [
                'error_message' => $errorMessage,
                'sql_query' => $sql,
                'sql_bindings' => $bindings,
                'error_code' => $e->getCode(),
                'exception_class' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            // Check for specific database errors
            if (str_contains($errorMessage, 'username') || str_contains($errorMessage, 'Duplicate entry')) {
                Log::error('=== USERNAME CONFLICT DETECTED ===', [
                    'error_message' => $errorMessage,
                    'sql_query' => $sql,
                    'sql_bindings' => $bindings,
                    'all_existing_usernames' => User::pluck('username')->toArray(),
                    'total_users' => User::count(),
                ]);
                
                return redirect()->route('register')
                    ->with('error', 'Username generation conflict. Please try again with a different name combination.');
            }
            
            return redirect()->route('register')
                ->with('error', 'Account creation failed due to a database error. Please check logs for details.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('=== REGISTRATION STEP 1 FAILED (General Exception) ===', [
                'error_message' => $e->getMessage(),
                'exception_class' => get_class($e),
                'error_code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'full_trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
            ]);
            
            return redirect()->route('register')
                ->with('error', 'Account creation failed: ' . $e->getMessage());
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
        Log::info('=== COMPLETE REGISTRATION START ===', [
            'user_id' => Auth::id(),
            'timestamp' => now()->toDateTimeString(),
        ]);

        $user = Auth::user();
        $finishData = session('finish_registration');

        Log::info('Step 1: User and session data retrieved', [
            'user_id' => $user->id ?? 'null',
            'user_email' => $user->email ?? 'null',
            'has_finish_data' => !empty($finishData),
            'finish_data' => $finishData,
        ]);

        if (!$finishData) {
            Log::warning('Missing finish_registration session data', [
                'user_id' => $user->id ?? 'null',
                'session_keys' => array_keys(session()->all()),
            ]);
            return redirect()->route('finish.registration')
                ->with('error', 'Please select your membership first.');
        }

        try {
            $package = Package::findOrFail($finishData['package_id']);
            Log::info('Step 2: Package retrieved', [
                'package_id' => $package->id,
                'package_title' => $package->title,
                'package_price' => $package->price,
                'package_role' => $package->role,
            ]);
        } catch (\Exception $e) {
            Log::error('Package not found', [
                'package_id' => $finishData['package_id'] ?? 'null',
                'error' => $e->getMessage(),
            ]);
            return redirect()->route('finish.registration')
                ->with('error', 'Selected package not found. Please try again.');
        }

        $isBusinessSeller = $finishData['role'] === 'seller' && $package->price > 0;

        Log::info('Step 3: Business seller check', [
            'role' => $finishData['role'],
            'package_price' => $package->price,
            'is_business_seller' => $isBusinessSeller,
        ]);

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

        Log::info('Step 4: Validation rules defined', [
            'rules' => array_keys($rules),
            'has_payment_rules' => isset($rules['card_number']),
            'has_business_license_rules' => isset($rules['business_license']),
        ]);

        try {
            $validated = $request->validate($rules);
            Log::info('Step 5: Validation passed', [
                'validated_keys' => array_keys($validated),
                'has_id_document' => $request->hasFile('id_document'),
                'has_business_license' => $request->hasFile('business_license'),
            ]);
        } catch (\Illuminate\Validation\ValidationException $ve) {
            Log::error('Validation failed', [
                'errors' => $ve->errors()->toArray(),
                'request_data' => $request->except(['id_document', 'business_license']), // Exclude files from log
            ]);
            return back()->withErrors($ve->validator)->withInput();
        }

        Log::info('Step 6: Starting database transaction');

        DB::beginTransaction();

        try {
            Log::info('Step 7: Updating user with ID type');

            // Update user with ID type
            $user->id_type = $validated['id_type'];
            if ($isBusinessSeller) {
                $user->relationship_to_business = $validated['relationship_to_business'];
                Log::info('Step 7a: Added relationship_to_business', [
                    'relationship' => $validated['relationship_to_business'],
                ]);
            }

            Log::info('Step 8: Handling ID document upload', [
                'has_file' => $request->hasFile('id_document'),
            ]);

            // Handle ID document upload
            if ($request->hasFile('id_document')) {
                try {
                    $idFile = $request->file('id_document');
                    Log::info('Step 8a: ID file details', [
                        'original_name' => $idFile->getClientOriginalName(),
                        'mime_type' => $idFile->getMimeType(),
                        'size' => $idFile->getSize(),
                    ]);

                    $idPath = $idFile->store('user_documents/' . $user->id, 'public');
                    Log::info('Step 8b: ID file stored', ['path' => $idPath]);

                    $userDoc = UserDocument::create([
                        'user_id' => $user->id,
                        'doc_type' => 'id',
                        'path' => $idPath,
                        'filename' => $idFile->getClientOriginalName(),
                        'mime_type' => $idFile->getMimeType(),
                        'size' => $idFile->getSize(),
                        'verified' => false,
                    ]);

                    Log::info('Step 8c: UserDocument created', ['doc_id' => $userDoc->id ?? 'null']);
                } catch (\Exception $docEx) {
                    Log::error('ID document upload failed', [
                        'error' => $docEx->getMessage(),
                        'trace' => $docEx->getTraceAsString(),
                    ]);
                    throw $docEx;
                }
            }

            Log::info('Step 9: Handling Business License upload', [
                'is_business_seller' => $isBusinessSeller,
                'has_file' => $request->hasFile('business_license'),
            ]);

            // Handle Business License upload
            if ($isBusinessSeller && $request->hasFile('business_license')) {
                try {
                    $licenseFile = $request->file('business_license');
                    Log::info('Step 9a: Business license file details', [
                        'original_name' => $licenseFile->getClientOriginalName(),
                        'mime_type' => $licenseFile->getMimeType(),
                        'size' => $licenseFile->getSize(),
                    ]);

                    $licensePath = $licenseFile->store('user_documents/' . $user->id, 'public');
                    Log::info('Step 9b: Business license file stored', ['path' => $licensePath]);

                    $user->business_license_path = $licensePath;

                    $licenseDoc = UserDocument::create([
                        'user_id' => $user->id,
                        'doc_type' => 'business_license',
                        'path' => $licensePath,
                        'filename' => $licenseFile->getClientOriginalName(),
                        'mime_type' => $licenseFile->getMimeType(),
                        'size' => $licenseFile->getSize(),
                        'verified' => false,
                    ]);

                    Log::info('Step 9c: Business license UserDocument created', ['doc_id' => $licenseDoc->id ?? 'null']);
                } catch (\Exception $licenseEx) {
                    Log::error('Business license upload failed', [
                        'error' => $licenseEx->getMessage(),
                        'trace' => $licenseEx->getTraceAsString(),
                    ]);
                    throw $licenseEx;
                }
            }

            Log::info('Step 10: Processing payment/subscription', [
                'role' => $finishData['role'],
                'requires_payment' => ($finishData['role'] === 'buyer' || $isBusinessSeller),
            ]);

            // Process payment if required
            if ($finishData['role'] === 'buyer' || $isBusinessSeller) {
                Log::info('Step 10a: Creating subscription for paid package');
                try {
                    $subscription = Subscription::create([
                        'user_id' => $user->id,
                        'package_id' => $package->id,
                        'starts_at' => now(),
                        'ends_at' => $package->duration_days ? now()->addDays($package->duration_days) : null,
                        'status' => 'pending',
                    ]);

                    Log::info('Step 10b: Subscription created', ['subscription_id' => $subscription->id ?? 'null']);

                    Log::info('Step 10c: Creating payment record');
                    $payment = Payment::create([
                        'user_id' => $user->id,
                        'subscription_id' => $subscription->id,
                        'amount' => $package->price,
                        'method' => 'credit_card',
                        'status' => 'pending', // Will be updated after payment gateway confirmation
                        'metadata' => [
                            'card_last4' => substr($validated['card_number'], -4),
                        ],
                    ]);

                    Log::info('Step 10d: Payment created', ['payment_id' => $payment->id ?? 'null']);
                } catch (\Exception $paymentEx) {
                    Log::error('Payment/Subscription creation failed', [
                        'error' => $paymentEx->getMessage(),
                        'trace' => $paymentEx->getTraceAsString(),
                    ]);
                    throw $paymentEx;
                }
            } else {
                Log::info('Step 10a: Creating subscription for free package (Individual Seller)');
                try {
                    $subscription = Subscription::create([
                        'user_id' => $user->id,
                        'package_id' => $package->id,
                        'starts_at' => now(),
                        'ends_at' => null,
                        'status' => 'active',
                    ]);

                    Log::info('Step 10b: Free subscription created', ['subscription_id' => $subscription->id ?? 'null']);
                } catch (\Exception $subEx) {
                    Log::error('Free subscription creation failed', [
                        'error' => $subEx->getMessage(),
                        'trace' => $subEx->getTraceAsString(),
                    ]);
                    throw $subEx;
                }
            }

            Log::info('Step 11: Assigning role and marking registration complete', [
                'role' => $finishData['role'],
            ]);

            // Assign role and mark registration as complete
            $user->role = $finishData['role'];
            $user->registration_complete = true;
            $user->save();

            Log::info('Step 12: User updated successfully', [
                'user_id' => $user->id,
                'role' => $user->role,
                'registration_complete' => $user->registration_complete,
            ]);

            Log::info('Step 13: Committing transaction');
            DB::commit();
            Log::info('Step 14: Transaction committed successfully');

            // Clear session
            $request->session()->forget('finish_registration');
            Log::info('Step 15: Session cleared');

            Log::info('Step 16: Sending registration complete email');
            // Send Email #2: Registration Complete
            try {
                $this->sendRegistrationCompleteEmail($user);
                Log::info('Step 16a: Registration complete email sent');
            } catch (\Exception $emailEx) {
                Log::warning('Failed to send registration complete email', [
                    'error' => $emailEx->getMessage(),
                ]);
                // Don't fail registration if email fails
            }

            Log::info('=== COMPLETE REGISTRATION SUCCESS ===', [
                'user_id' => $user->id,
                'role' => $user->role,
            ]);

            // Redirect to appropriate dashboard based on role
            $role = trim(strtolower($user->role ?? ''));
            Log::info('Step 17: Redirecting based on role', ['role' => $role]);

            if ($role === 'seller') {
                return redirect()->route('dashboard.seller')
                    ->with('success', 'Registration completed successfully! Welcome to CayMark.');
            } elseif ($role === 'buyer') {
                return redirect()->route('welcome') // listings page
                    ->with('success', 'Registration completed successfully! Welcome to CayMark.');
            }

            // Fallback to default dashboard if role is invalid
            return redirect()->route('dashboard.default')
                ->with('success', 'Registration completed successfully! Welcome to CayMark.');

        } catch (\Illuminate\Database\QueryException $qe) {
            DB::rollBack();
            Log::error('=== COMPLETE REGISTRATION FAILED (Database QueryException) ===', [
                'error_message' => $qe->getMessage(),
                'sql' => $qe->getSql() ?? 'N/A',
                'bindings' => $qe->getBindings() ?? [],
                'error_code' => $qe->getCode(),
                'file' => $qe->getFile(),
                'line' => $qe->getLine(),
                'trace' => $qe->getTraceAsString(),
            ]);
            return back()->with('error', 'Registration completion failed due to a database error. Please try again.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('=== COMPLETE REGISTRATION FAILED (General Exception) ===', [
                'error_message' => $e->getMessage(),
                'exception_class' => get_class($e),
                'error_code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'user_id' => $user->id ?? 'null',
            ]);
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
