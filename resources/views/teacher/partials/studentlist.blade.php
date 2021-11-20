<div class="row row-cols-1 row-cols-md-5 g-2" id="posts-list">
@foreach ($students as $student)
    @php
        $rateSvg = "star-fill-red4.svg";
        if (isset($student->rate)) {
            $rateSvg = "star-fill-red" . $student->rate . ".svg";
        }
        $markSvg = "heart-fill-red4.svg";
        if (isset($student->mark_num)) {
            if($student->mark_num < 4) {
                $markSvg = "heart-fill-red3.svg";
            } else if ($student->mark_num < 10) {
                $markSvg = "heart-fill-red2.svg";
            } else {
                $markSvg = "heart-fill-red1.svg";
            }
        }
    @endphp
    <div class="col">
        <div class="card h-100">
            <a href="/teacher/sb3player?postCode={{$student->post_code}}" target="_blank" style="padding: 5px;">
                <img class="card-img-top" value="{{ $student->posts_id }}" class="img-fluid" src="/posts/yuying3/{{$student->post_code}}_c.{{$student->cover_ext}}" alt=""></a>

            <div class="card-footer">
                <div class="row">
                    <div class="col">{{ @$student->order_num }} {{ $py->getFirstchar($student->username) }} {{ $student->username }}</div>
                    <div class="col">
                        <div class="row">
                            <div class="col">
                                <!-- <i class="bi bi-star-fill" style="color: 0xffffff"></i> -->
                                <img src="/img/{{$rateSvg}}">
                            </div>
                            <div class="col">
                                <img src="/img/{{$markSvg}}">
                            </div>
                        </div>
                    </div>
            </div>
        </div>
    </div>
    </div>
@endforeach
</div>