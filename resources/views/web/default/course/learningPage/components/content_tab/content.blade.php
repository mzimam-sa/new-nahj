@php
    $icon = '';
    $hintText= '';

    if ($type == \App\Models\WebinarChapter::$chapterSession) {
        $icon = 'video';
        $hintText = dateTimeFormat($item->date, 'j M Y  H:i') . ' | ' . $item->duration . ' ' . trans('public.min');
    } elseif ($type == \App\Models\WebinarChapter::$chapterFile) {
        $hintText = trans('update.file_type_'.$item->file_type) . ($item->volume > 0 ? ' | '.$item->getVolume() : '');

        $icon = $item->getIconByType();
    } elseif ($type == \App\Models\WebinarChapter::$chapterTextLesson) {
        $icon = 'file-text';
        $hintText= $item->study_time . ' ' . trans('public.min');
    }

    $checkSequenceContent = $item->checkSequenceContent();
    $sequenceContentHasError = (!empty($checkSequenceContent) and (!empty($checkSequenceContent['all_passed_items_error']) or !empty($checkSequenceContent['access_after_day_error'])));
@endphp

<div class=" d-flex align-items-start p-10 cursor-pointer {{ (!empty($checkSequenceContent) and $sequenceContentHasError) ? 'js-sequence-content-error-modal' : 'tab-item' }}"
     data-type="{{ $type }}"
     data-id="{{ $item->id }}"
     data-passed-error="{{ !empty($checkSequenceContent['all_passed_items_error']) ? $checkSequenceContent['all_passed_items_error'] : '' }}"
     data-access-days-error="{{ !empty($checkSequenceContent['access_after_day_error']) ? $checkSequenceContent['access_after_day_error'] : '' }}"
>

        <span class="chapter-icon bg-gray300 ml-10">
            <i data-feather="{{ $icon }}" class="text-gray" width="16" height="16"></i>
        </span>

    <div>
        <div class="">
            <span class="font-weight-500 font-14 text-dark-blue d-block">{{ $item->title }}</span>
            <span class="font-12 text-gray d-block">{{ $hintText }}</span>
        </div>


        <div class="tab-item-info mt-15">
            <p class="font-12 text-gray d-block">
                @php
                    $description = !empty($item->description) ? $item->description : (!empty($item->summary) ? $item->summary : '');
                @endphp

                {!! truncate($description, 150) !!}
            </p>

            @if($type == \App\Models\WebinarChapter::$chapterSession)
                @if(auth()->check() && (auth()->user()->id == $item->webinar->teacher_id || auth()->user()->isAdmin()))
                    <div class="mt-15">
                        <label class="font-weight-bold">قائمة المتدربين (تشييك الحضور):</label>
                        <form method="POST" action="{{ route('panel.sessions.attendance', ['session_id' => $item->id]) }}">
                            @csrf
                            <table class="table table-bordered table-sm mt-2">
                                <thead>
                                    <tr>
                                        <th>الطالب</th>
                                        <th>حضر؟</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $presentStudents = \App\Models\CourseLearning::where('session_id', $item->id)->pluck('user_id')->toArray();
                                    @endphp
                                    @foreach($item->webinar->getStudentsIds() as $studentId)
                                        @php $student = \App\User::find($studentId); @endphp
                                        @if($student)
                                        <tr>
                                            <td>
                                                <img src="{{ $student->getAvatar(30) }}" class="rounded-circle" width="30" height="30" alt="avatar">
                                                {{ $student->full_name }}
                                            </td>
                                            <td>
                                                <input type="checkbox" name="attendance[{{ $student->id }}]" value="1" @if(in_array($student->id, $presentStudents)) checked @endif>
                                            </td>
                                        </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                            <button type="submit" class="btn btn-primary btn-sm mt-2">حفظ الحضور</button>
                        </form>
                    </div>
                @else
                    <div class="mt-15">
    <span class="badge badge-secondary" style="font-size: 12px; padding: 6px 10px; border-radius: 6px; white-space: normal; text-align: right; display: inline-block;">
        سيتم تسجيل الحضور من قبل المدرب   
    </span>
</div>
                @endif
            @else
                <div class="d-flex align-items-center justify-content-between mt-15">
                    <label class="mb-0 ml-10 cursor-pointer font-weight-normal font-14 text-dark-blue" for="readToggle{{ $type }}{{ $item->id }}">{{ trans('public.i_passed_this_lesson') }}</label>
                    <div class="custom-control custom-switch">
                        <input type="checkbox" @if($sequenceContentHasError) disabled @endif id="readToggle{{ $type }}{{ $item->id }}" data-item-id="{{ $item->id }}" data-item="{{ $type }}_id" value="{{ $item->webinar_id }}" class="js-passed-lesson-toggle custom-control-input" @if(!empty($item->checkPassedItem())) checked @endif>
                        <label class="custom-control-label" for="readToggle{{ $type }}{{ $item->id }}"></label>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
