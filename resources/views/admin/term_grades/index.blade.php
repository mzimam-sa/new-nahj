@extends('admin.layouts.app')

@section('content')
<div class="section-header">
    <h1>درجات الترم</h1>
</div>

<div class="section-body">
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>اسم الطالب</th>
                            <th>الكورس</th>
                            <th>الدرجة</th>
                            <th>ملاحظات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($grades as $grade)
                        <tr>
                            <td>{{ $grade->id }}</td>
                            <td>{{ optional($grade->student)->full_name ?? '-' }}</td>
                            <td>{{ optional($grade->webinar)->title ?? '-' }}</td>
                            <td>{{ $grade->score }}</td>
                            <td>{{ $grade->notes }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $grades->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
