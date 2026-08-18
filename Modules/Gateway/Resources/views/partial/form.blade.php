<form action="{{ route(config($addon->getAlias() . '.store_route')) }}" method="post" enctype="multipart/form-data">
    @csrf
    <div class="addon-modal-body">
        @php
            $fields = config($addon->getAlias() . '.fields');
        @endphp
        @forelse ($fields as $name => $field)
            @php
                $value = old($name, getValueForForm($module, $name));
            @endphp
            <div class="addon-modal-form-row">
                <label class="addon-modal-label">
                    {{ __($field['label']) }}
                    @if (isset($field['required']) && $field['required'])
                        <span class="addon-modal-danger">*</span>
                    @endif
                </label>
                <div class="addon-modal-field">
                    @if ($field['type'] == 'text')
                        <input type="text"
                            class="addon-modal-input {{ isset($field['class']) ? $field['class'] : '' }}"
                            placeholder="{{ $field['placeholder'] ?? $field['label'] }}" name="{{ $name }}"
                            {{ isset($field['required']) && $field['type'] ? 'required' : '' }}
                            value="{{ $value }}">
                    @elseif ($field['type'] == 'url')
                        <input type="url"
                            class="addon-modal-input {{ isset($field['class']) ? $field['class'] : '' }}"
                            placeholder="{{ $field['placeholder'] ?? $field['label'] }}" name="{{ $name }}"
                            {{ isset($field['required']) && $field['type'] ? 'required' : '' }} 
                            {{ isset($field['readonly']) && $field['type'] ? 'readonly' : '' }} 
                            value="{{ $value }}">
                    @elseif ($field['type'] == 'textarea')
                        <textarea class="addon-modal-input {{ isset($field['class']) ? $field['class'] : '' }}"
                            placeholder="{{ $field['placeholder'] ?? $field['label'] }}" name="{{ $name }}"
                            {{ isset($field['required']) && $field['type'] ? 'required' : '' }}>{{ $value ?? '' }}</textarea>
                    @elseif ($field['type'] == 'select')
                        <select class="addon-modal-input {{ isset($field['class']) ? $field['class'] : '' }}"
                            name="{{ $name }}"
                            {{ isset($field['required']) && $field['type'] ? 'required' : '' }}>
                            @forelse ($field['options'] as $option => $val)
                                <option
                                    {{ $value == $val ? 'selected' : '' }}
                                    value="{{ $val }}">
                                    {{ $option }}
                                </option>
                            @empty
                            @endforelse
                        </select>
                    @elseif ($field['type'] == 'file')
                        <input type="file" name="{{ $name }}" id="{{ $name }}" class="addon-modal-input" accept="image/*" {{ isset($field['required']) && $field['type'] ? 'required' : '' }}>
                        @if($value)
                            <img src="{{ url('/') }}/Modules/{{ $addon->getName() }}/Resources/assets/{{ $value }}" alt="Preview">
                        @endif
                    @endif
                    @if (isset($field['note']))
                        <div class="mt-2 text-muted" style="font-size: 0.775rem;">
                            <span class="badge bg-info text-white me-2" style="font-size: 0.65rem;">{{ __('Note') }}</span>{{ $field['note'] }}
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center py-4 text-muted">{{ __('No configuration fields available.') }}</div>
        @endforelse
    </div>
    <div class="addon-modal-foot">
        <button type="button" class="addon-modal-cancel">{{ __('Cancel') }}</button>
        <button type="submit" class="addon-modal-submit">{{ __('Save Changes') }}</button>
    </div>
</form>
