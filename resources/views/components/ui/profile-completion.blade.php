@props([
    'user' => null,
])

@php
    use App\Helpers\ProfileCompletionHelper;

    $user = $user ?? auth()->user();
    $data = $user ? ProfileCompletionHelper::evaluate($user) : ['percent' => 100, 'complete' => true, 'missingFields' => []];
@endphp

@if($user && ! $data['complete'])
    <div {{ $attributes->merge(['class' => 'cm-profile-completion']) }}>
        <div class="cm-profile-completion__head">
            <div>
                <h3 class="cm-profile-completion__title">Complete your profile</h3>
                <p class="cm-profile-completion__subtitle">Finish the steps below to unlock the full CayMark experience.</p>
            </div>
            <span class="cm-profile-completion__percent" aria-label="{{ $data['percent'] }} percent complete">{{ $data['percent'] }}%</span>
        </div>
        <div class="cm-profile-completion__track" role="progressbar" aria-valuenow="{{ $data['percent'] }}" aria-valuemin="0" aria-valuemax="100">
            <div class="cm-profile-completion__bar" style="width: {{ $data['percent'] }}%;"></div>
        </div>
        @if(count($data['missingFields']) > 0)
            <ul class="cm-profile-completion__list">
                @foreach($data['missingFields'] as $field)
                    <li>
                        @if(! empty($field['url']))
                            <a href="{{ $field['url'] }}" class="cm-profile-completion__link">
                                <span class="material-icons-round" aria-hidden="true">chevron_right</span>
                                {{ $field['label'] }}
                            </a>
                        @else
                            <span class="cm-profile-completion__link cm-profile-completion__link--static">
                                <span class="material-icons-round" aria-hidden="true">chevron_right</span>
                                {{ $field['label'] }}
                            </span>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
@endif
