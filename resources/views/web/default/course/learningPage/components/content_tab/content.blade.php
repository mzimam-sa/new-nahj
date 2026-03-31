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

        @if($type == \App\Models\WebinarChapter::$chapterSession && !empty($item->link))
            <a href="{{ $item->getJoinLink() }}" target="_blank" class="chapter-icon bg-gray300 ml-10" title="دخول الجلسة">
                <i data-feather="{{ $icon }}" class="text-gray" width="16" height="16"></i>
            </a>
        @else
            <span class="chapter-icon bg-gray300 ml-10">
                <i data-feather="{{ $icon }}" class="text-gray" width="16" height="16"></i>
            </span>
        @endif

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
                        <label class="font-weight-bold">قائمة المتدربين (تحضير الطلاب):</label>
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
                            <a href="{{ route('panel.sessions.attendance.export', ['session_id' => $item->id]) }}"
                               class="btn btn-sm mt-2 ml-2 export-attendance-btn"
                               style="background: linear-gradient(90deg, #00c7b2 0%, #1e90ff 100%); color: #fff; border: none; box-shadow: 0 2px 8px rgba(30,144,255,0.08); font-weight: bold; letter-spacing: 0.5px;">
                                <i class="fa fa-file-excel-o" style="margin-left: 6px;"></i>
                                تصدير الحضور (Excel)
                            </a>
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
                <!-- تم حذف جملة اجتياز الدرس بناءً على طلب العميل -->
            @endif
        </div>
    </div>
</div>
