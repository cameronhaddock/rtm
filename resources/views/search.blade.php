@extends('layout.default')
@section('page_head')
<div class="jumbotron jumbotron-fluid bg-info">
    <div class="container">
          <form action="{{ route('search') }}" method="get">
            <div class="row">
                <div class="col-md-12">
                  <div class="form-check form-check-inline">
                    <label class="form-check-label" for="inlineCheckbox1">Search in </label>
                  </div>
                  <div class="form-check form-check-inline">
                    <input 
                      class="form-check-input" 
                      type="checkbox" 
                      name='quilifiers[]' 
                      @if(in_array("name",$qualifiers)) checked @endif
                      value="name">
                    <label class="form-check-label" for="inlineCheckbox1">Name </label>
                  </div>
                  <div class="form-check form-check-inline">
                    <input 
                      class="form-check-input" 
                      type="checkbox" 
                      name='quilifiers[]' 
                       @if(in_array("description",$qualifiers)) checked @endif
                      value="description">
                    <label class="form-check-label" for="inlineCheckbox2">Description</label>
                  </div>
                </div>
            </div>
            <div class="row">
              <div class="col-md-8">
                <div class="input-group md-form form-sm form-2 pl-0">
                      <input class="form-control my-0 py-1 red-border" 
                      type="text" 
                      name="keyword"
                      id="keyword" 
                      placeholder="Enter your keyword" 
                      value="{{ $keyword }}"
                      aria-label="Search" >
                      <div class="input-group-append">
                        <button type="submit" class="input-group-text cyan lighten-2">Search</button>
                        
                      </div>
                </div>
              </div>
            </div>
          </form>
    </div>
</div>
@endsection
@section('content')
  
  <div class="col-md-12">
    @if($total_items > 0)
    <table class="table table-hover table-striped">
      <thead>
        <tr>
          <th scope="col">Repository Name</th>
          <th scope="col">Username</th>
          <th scope="col">Description</th>
          <th scope="col">Stars</th>
        </tr>
      </thead>
      <tbody>
      @foreach($items as $item)
        <tr>
            <td><a href="{{ $item->html_url }}" target="_blank">{{ $item->name }}</a></td>
            <td>{{ explode('/', $item->full_name)[0] }}</td>
            <td>{{ $item->description }}</td>
            <td>{{ $item->stargazers_count }}</td>
          </tr>
      @endforeach
      </tbody>
    </table>
  </div>
  <div class="col-md-12">
    {!! $pages !!}
  </div>

  <div class="col-md-12">
    <h4>Search summarized</h4>
    <table class="table table-bordered summary-table">
      <thead>
        <tr>
          <th scope="col">Language</th>
          <th scope="col">Number of Repos</th>
        </tr>
      </thead>
      <tbody>
        @foreach($languages as $key=>$value)
          <tr>
            <td>{{ $key }}</td>
            <td>{{ $value }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>

  </div>
  @endif
@endsection