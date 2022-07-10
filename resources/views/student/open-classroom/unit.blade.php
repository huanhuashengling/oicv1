@extends('layouts.student')

@section('content')
<div class="container">
    <div class="row row-cols-1 row-cols-md-4 g-2">
    {!! Breadcrumbs::render('unit', $unit) !!}
    @foreach($lessons as $key => $lesson)
    <div class="col">
    <div class="card text-center">
      <div class="card-body">
        <a href="/student/open-classroom/lesson/{{$lesson->id}}">
        <img src="{{$planetUrl}}" alt="..." class="img-circle"></a>
            <h4>{{$lesson->title}}</h4>
            <p class="text-left">{{$lesson->subtitle}}</p>
      </div>
    </div>
</div>
    @endforeach
</div>
</div>
@endsection

@section('scripts')
    <script src="/js/student/open-classroom.js?v={{rand()}}"></script>
@endsection