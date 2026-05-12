<div class="webinar-card">
    <figure>
        <div class="image-box">
            @if($webinar->bestTicket() < $webinar->price)
                <span class="badge badge-danger">{{ trans('public.offer',['off' => $webinar->bestTicket(true)['percent']]) }}</span>
            @elseif(empty($isFeature) and !empty($webinar->feature))
                <span class="badge badge-warning">{{ trans('home.featured') }}</span>
            @elseif($webinar->type == 'webinar')
                @if($webinar->start_date > time())
                    <span class="badge badge-primary">{{  trans('panel.not_conducted') }}</span>
                @elseif($webinar->isProgressing())
                    <span class="badge badge-secondary">{{ trans('webinars.in_progress') }}</span>
                @else
                    <!--<span class="badge badge-secondary">{{ trans('public.finished') }}</span>-->
                @endif
            @elseif(!empty($webinar->type))
                {{-- <span class="badge badge-primary">{{ trans('webinars.'.$webinar->type) }}</span> --}}
            @endif

            <a href="{{ $webinar->getUrl() }}">
                <img src="{{ $webinar->getImage() }}" class="img-cover" alt="{{ $webinar->title }}">
            </a>


            @if($webinar->checkShowProgress())
                <div class="progress">
                    <span class="progress-bar" style="width: {{ $webinar->getProgress() }}%"></span>
                </div>
            @endif

            @if($webinar->type == 'webinar')
                <a href="{{ $webinar->addToCalendarLink() }}" target="_blank" class="webinar-notify d-flex align-items-center justify-content-center">
                    <i data-feather="bell" width="20" height="20" class="webinar-icon"></i>
                </a>
            @endif
        </div>

        <figcaption class="webinar-card-body">

            <a href="{{ $webinar->getUrl() }}">
                <h3 class="mt-15 webinar-title font-weight-bold font-16 text-dark-blue">{{ clean($webinar->title,'title') }}</h3>
            </a>

            <div class="webinar-price-box mt-25">
                @if(!empty($isRewardCourses) and !empty($webinar->points))
                        <span class="text-warning real font-14">{{ $webinar->points }} {{ trans('update.points') }}</span>
                    @elseif(!empty($webinar->price) and $webinar->price > 0)
                        @if($webinar->bestTicket() < $webinar->price)
                            <span class="real">{{ handlePrice($webinar->bestTicket(), true, true, false, null, true) }}</span>
                            <span class="off ml-10">{{ handlePrice($webinar->price, true, true, false, null, true) }}</span>
                        @else
                            <span class="real">{{ handlePrice($webinar->price, true, true, false, null, true) }}</span>
                        @endif
                    @else
                        <span class="real font-14">{{ trans('public.free') }}</span>
                @endif
            </div>
        </figcaption>
    </figure>
</div>
