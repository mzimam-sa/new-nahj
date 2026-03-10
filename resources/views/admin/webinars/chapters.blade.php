@extends('admin.layouts.app')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>{{ trans('webinars.chapters') }}</h1>
    </div>
    <div class="section-body">
        <div class="card">
            <div class="card-body">
                @if(isset($webinar) && $webinar->chapters->count())
                    <ul class="list-group">
                        @foreach($webinar->chapters as $chapter)
                            <li class="list-group-item">
                                {{ $chapter->title }}
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="alert alert-warning">{{ trans('webinars.chapters') }} {{ trans('admin/main.not_found') }}</div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
