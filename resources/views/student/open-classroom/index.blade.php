@extends('layouts.student')

@section('content')
<div class="container">
    <div class="row row-cols-1 row-cols-md-4 g-2">
    {!! Breadcrumbs::render('open-classroom') !!}
    @foreach($courses as $key => $course)
    <div class="col">
    <div class="card  h-200 text-center">
      <div class="card-body">
        <a href="/student/open-classroom/course/{{$course->id}}">
        <img src="{{$planetUrl}}" alt="..." class="img-circle"></a>
            <h4>{{$course->title}}</h4>
            <p class="text-left">{{$course->description}}</p>
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