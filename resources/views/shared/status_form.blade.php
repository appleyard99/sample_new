<form action="{{ route('statuses.store') }}" method="post">
    @include('shared.errors')
    {{ csrf_field() }}
    <textarea name="content" class ="form-control"  rows="3" placeholder="聊聊新鲜事...">
        {{ old('content') }}
    </textarea>
    <button type="submit" class="btn btn-primary pull-right">发布</button>
</form>