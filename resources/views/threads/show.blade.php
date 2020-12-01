@extends('layouts.app')

@section('content')
    <thread-view :initial-replies-count="{{$thread->replies_count}}" inline-template>
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <div class="level">
                            <span class="flex">
                                <h1>{{$thread->title}}</h1>
                                <p>Posted by <a href="{{route('profile', $thread->owner)}}">{{$thread->owner->name}}</a></p>
                            </span>

                                @can ('update', $thread)
                                    <form action="{{$thread->path()}}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger">Delete</button>
                                    </form>
                                @endcan
                            </div>

                        </div>
                        <div class="card-body">
                            {{$thread->body}}
                        </div>
                    </div>

{{--                    Replies component is responsible for requesting all of the data that it needs,--}}
{{--                    rather than having our server side pass to it.--}}
                    <replies @added="repliesCount++" @removed="repliesCount--"></replies>

                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <p>
                                This thread was published {{$thread->created_at->diffForHumans()}} by
                                <a href="#">{{$thread->owner->name}}</a> and has <span v-text="repliesCount"></span>
                                {{Str::plural('comment', $thread->repliesCount)}}.
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </thread-view>
@endsection
