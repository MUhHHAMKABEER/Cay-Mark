<?php

namespace App\Helpers;

use App\Models\User;
use Illuminate\Support\Facades\Route;

class ProfileCompletionHelper
{
    /**
     * @return array{percent: int, complete: bool, missingFields: array<int, array{label: string, field: string, route?: string, url?: string}>}
     */
    public static function evaluate(User $user): array
    {
        $checks = self::checksFor($user);
        $total = count($checks);
        $complete = 0;
        $missing = [];

        foreach ($checks as $check) {
            if ($check['filled']) {
                $complete++;
                continue;
            }

            $entry = [
                'label' => $check['label'],
                'field' => $check['field'],
            ];

            if (! empty($check['route']) && Route::has($check['route'])) {
                $entry['route'] = $check['route'];
                $entry['url'] = route($check['route']);
            } elseif (! empty($check['url'])) {
                $entry['url'] = $check['url'];
            }

            $missing[] = $entry;
        }

        $percent = $total > 0 ? (int) round(($complete / $total) * 100) : 100;

        return [
            'percent' => min(100, max(0, $percent)),
            'complete' => $missing === [],
            'missingFields' => $missing,
        ];
    }

    public static function isComplete(User $user): bool
    {
        return self::evaluate($user)['complete'];
    }

    /**
     * @return array<int, array{field: string, label: string, filled: bool, route?: string, url?: string}>
     */
    protected static function checksFor(User $user): array
    {
        $accountRoute = match ($user->role) {
            User::ROLE_SELLER => 'seller.account',
            User::ROLE_BUYER => 'buyer.user',
            default => 'profile.edit',
        };

        $checks = [
            [
                'field' => 'name',
                'label' => 'Full name',
                'filled' => filled(trim((string) $user->name)),
                'route' => $accountRoute,
            ],
            [
                'field' => 'email',
                'label' => 'Email address',
                'filled' => filled($user->email),
                'route' => $accountRoute,
            ],
            [
                'field' => 'phone',
                'label' => 'Phone number',
                'filled' => filled($user->phone),
                'route' => $accountRoute,
            ],
            [
                'field' => 'phone_verified_at',
                'label' => 'Verified phone number',
                'filled' => $user->phone_verified_at !== null,
                'route' => $accountRoute,
            ],
            [
                'field' => 'nationality',
                'label' => 'Nationality',
                'filled' => filled($user->nationality),
                'route' => $accountRoute,
            ],
            [
                'field' => 'island',
                'label' => 'Island',
                'filled' => filled($user->island),
                'route' => $accountRoute,
            ],
            [
                'field' => 'dob',
                'label' => 'Date of birth',
                'filled' => $user->dob !== null,
                'route' => $accountRoute,
            ],
            [
                'field' => 'gender',
                'label' => 'Gender',
                'filled' => filled($user->gender),
                'route' => $accountRoute,
            ],
            [
                'field' => 'id_type',
                'label' => 'Primary ID type',
                'filled' => filled($user->id_type),
                'route' => $user->registration_complete ? $accountRoute : 'finish.registration',
            ],
            [
                'field' => 'id_type_2',
                'label' => 'Secondary ID type',
                'filled' => filled($user->id_type_2),
                'route' => $user->registration_complete ? $accountRoute : 'finish.registration',
            ],
            [
                'field' => 'id_documents',
                'label' => 'Government ID documents',
                'filled' => $user->documents()->whereIn('doc_type', ['id', 'id_2'])->exists(),
                'route' => $user->registration_complete ? $accountRoute : 'finish.registration.complete.show',
            ],
            [
                'field' => 'registration_complete',
                'label' => 'Registration completed',
                'filled' => $user->isRegistrationComplete(),
                'route' => 'finish.registration',
            ],
        ];

        if ($user->role === User::ROLE_SELLER) {
            $isBusiness = filled($user->business_license_path) || filled($user->relationship_to_business);

            if ($isBusiness) {
                $checks[] = [
                    'field' => 'business_license_path',
                    'label' => 'Business license',
                    'filled' => filled($user->business_license_path),
                    'route' => $user->registration_complete ? $accountRoute : 'finish.registration.complete.show',
                ];
                $checks[] = [
                    'field' => 'relationship_to_business',
                    'label' => 'Relationship to business',
                    'filled' => filled($user->relationship_to_business),
                    'route' => $user->registration_complete ? $accountRoute : 'finish.registration.complete.show',
                ];
            }

            $checks[] = [
                'field' => 'payout_method',
                'label' => 'Payout method',
                'filled' => $user->getActivePayoutMethod() !== null,
                'route' => 'seller.account',
            ];
        }

        return $checks;
    }
}
