<reply :attributes="{{$reply}}" inline-template>

    <div id="reply-{{$reply->id}}" class="card mb-4">
        <div class="card-header">
            <div class="level">

                <h5 class="flex">
                    <a href="{{route('profile', $reply->owner)}}">
                        {{$reply->owner->name}}
                    </a> said {{$reply->created_at->diffForHumans()}}
                </h5>

                @auth
                    <div>
                        <favorite :reply="{{$reply}}"></favorite>
                    </div>
                @endauth
            </div>
        </div>

        <div class="card-body">
            <div v-if="editing">
                <div class="form-group">
                    <textarea class="form-control" v-model="body"></textarea>
                </div>
                <div class="level">
                    <button class="btn btn-outline-primary btn-sm mr-2" @click="update">
                        Save
                    </button>
                    <button class="btn btn-link btn-sm mr-2" @click="editing = false">
                        Cancel
                    </button>
                </div>
            </div>
            <div v-else v-text="body"></div>
        </div>

        @can('update', $reply)
            <div class="card-footer level">
                <button class="btn btn-outline-primary btn-sm mr-2" @click="editing = true">Edit</button>
                <button class="btn btn-outline-danger btn-sm mr-2" @click="destroy">Delete</button>
            </div>
        @endcan
    </div>

</reply>
