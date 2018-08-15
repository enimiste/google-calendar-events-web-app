@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Events</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if(!session('google-synced', false))
                        <button type="button" class="btn btn-primary">Sync Google</button>
                    @else
                        <button type="button" class="btn btn-warning">Un-sync Google</button>
                    @endif
                    <pre>
                        <code>
                        {!! json_encode($events) !!}
                        </code>
                    </pre>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
