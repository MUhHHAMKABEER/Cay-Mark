<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\User;
use App\Models\UserDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
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
     * Step 1: personal information + document upload.
     * Stores step1 data and temp document references in session.
     */
    public function step1(Request $request)
    {
        // mark UI step
        $request->session()->put('registration_step', 1);

        $eighteenYearsAgo = now()->subYears(18)->toDateString();

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'email_confirmation' => 'required|same:email',
            'phone' => 'required|string|max:20',
            'gender' => 'required|in:Male,Female',
            'password' => ['required', 'confirmed', Password::min(8)],
            'username' => 'required|alpha_dash|unique:users,username|max:255',
            'dob' => 'required|date|before_or_equal:' . $eighteenYearsAgo,
            'nationality' => 'required|string|max:255',
            'island' => 'required|in:' . implode(',', $this->bahamianIslands),
            'id_documents' => 'required|array|min:2|max:2',
            'id_documents.*' => 'file|mimes:jpg,jpeg,png,pdf|max:5120',
        ], [
            'dob.before_or_equal' => 'You must be 18 years or older to register.',
            'id_documents.min' => 'Please upload at least 2 government-issued ID documents.',
            'id_documents.max' => 'Please upload exactly 2 government-issued ID documents.',
        ]);

        // Keep only the fields we need in session (exclude confirmations and tokens)
        $step1Data = $request->except([
            'id_documents', '_token', 'password_confirmation', 'email_confirmation'
        ]);

        // store step1 values (non-file) in session
        $request->session()->put('registration.step1', $step1Data);

        // store uploaded files temporarily
        if ($request->hasFile('id_documents')) {
            $documents = [];
            foreach ($request->file('id_documents') as $file) {
                try {
                    // store on local disk in a temp folder
                    $tempPath = $file->store('temp/documents', 'local');

                    $documents[] = [
                        'original_name' => $file->getClientOriginalName(),
                        'temp_path' => $tempPath,
                        'mime_type' => $file->getMimeType(),
                        'size' => $file->getSize(),
                    ];
                } catch (\Exception $ex) {
                    Log::error('Failed to store id_document: ' . $ex->getMessage(), ['exception' => $ex]);
                    return back()->withErrors(['id_documents' => 'Failed to store uploaded documents. Please try again.']);
                }
            }

            // store temp docs info in session
            $request->session()->put('registration.documents', $documents);
        }

        // advance UI to step 2
        $request->session()->put('registration_step', 2);

        return redirect()->route('register');
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
}
