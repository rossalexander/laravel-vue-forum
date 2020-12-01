@extends('layouts.app')
@section('content')
    <div class="container">

        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <h1>
                    {{$profileUser->name}}
                </h1>

                @forelse($activities as $date => $activity)
                    <h2 class="popover-header">{{$date}}</h2>
                    @foreach($activity as $record)
                        @if(view()->exists("profiles.activities.{$record->type}"))
                            <div class="mb-3">
                                @include ("profiles.activities.{$record->type}", ['activity' => $record])
                            </div>
                        @endif
                    @endforeach
                    @empty
                    <p>No activity for {{$profileUser->name}} yet :/</p>
                @endforelse

            </div>
        </div>

    </div>
@endsection
