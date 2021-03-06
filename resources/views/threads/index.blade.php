@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                @forelse($threads as $thread)
                    <div class="card mb-3">
                        <div class="card-header">
                            <div class="level">
                                <div class="flex">
                                    <h4>
                                        <a href="{{$thread->path()}}">

                                            @if(auth()->check() && $thread->hasUpdatesFor(auth()->user()))
                                                <strong>{{$thread->title}}</strong>
                                            @else
                                                {{$thread->title}}
                                            @endif
                                        </a>
                                        <h6><a href="{{route('profile', $thread->owner)}}">{{$thread->owner->name}}</a></h6>
                                    </h4>
                                </div>
                                <a href="{{$thread->path()}}"><strong>{{$thread->replies_count}} {{Str::plural('reply', $thread->replies_count)}}</strong></a>
                            </div>
                        </div>

                        <div class="card-body">

                            <article>
                                <div class="body">{{$thread->body}}</div>
                            </article>
                        </div>
                    </div>
                @empty
                    <p>It looks like there are no threads for this channel.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection
