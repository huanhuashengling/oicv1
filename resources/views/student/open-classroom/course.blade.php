@extends('layouts.student')

@section('content')
<div class="container">
    <div class="row row-cols-1 row-cols-md-4 g-2">
    {!! Breadcrumbs::render('course', $course) !!}
    @foreach($units as $key => $unit)
    <div class="col">
    <div class="card text-center">
      <div class="card-body">
        <a href="/student/open-classroom/unit/{{$unit->id}}">
        <img src="{{$planetUrl}}" alt="..." class="img-circle"></a>
            <h4>{{$unit->title}}</h4>
            <p class="text-left">{{$unit->description}}</p>
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