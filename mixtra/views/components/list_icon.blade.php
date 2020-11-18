<select class="form-control custom-select" name="icon">
    <option value="">** Select an Icon</option>
    @foreach($fontawesome as $font)
        <option value='fa fa-{{$font}}' {{ ($row->icon == "fa fa-$font")?"selected":"" }} data-label='{{$font}}'>{{$font}}</option>
    @endforeach
</select>