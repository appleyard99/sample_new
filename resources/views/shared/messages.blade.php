@foreach(['danger','warning','success','info'] as $msg)
    @if(session()->has($msg))
        <div class="flash-message">
            <p calss="alert alert-{{ $msg }}">
             {{ session()->get($msg) }}
            </p>
        </div>
    @endif
@endforeach