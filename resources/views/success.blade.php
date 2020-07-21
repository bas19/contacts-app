@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Success!</div>
                <div class="card-body text-center">
                   <h1 class="mt-5">Thank you for registering.</h1>
                   <a href="/home" class="btn btn-lg btn-primary mt-5 mb-5">Continue</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
