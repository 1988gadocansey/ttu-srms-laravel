@extends('layouts.directory')


@section('style')
<!--  contact list functions -->

@endsection
@section('content')
<div class="md-card-content">
    @if(Session::has('success'))
    <div style="text-align: center" class="uk-alert uk-alert-success" data-uk-alert="">
        {!! Session::get('success') !!}
    </div>
    @endif

    @if (count($errors) > 0)

    <div class="uk-form-row">
        <div class="uk-alert uk-alert-danger" style="background-color: red;color: white">

            <ul>
                @foreach ($errors->all() as $error)
                <li> {{  $error  }} </li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif
</div>

   
<h3 class="heading_b uk-margin-bottom">Contact List</h3>
<div class="md-card uk-margin-medium-bottom">
    <div class="md-card-content">
        <div class="uk-grid" data-uk-grid-margin>
            <div class="uk-width-medium-1-2">
                <div class="uk-vertical-align">
                    <div class="uk-vertical-align-middle">
                        <ul id="contact_list_filter" class="uk-subnav uk-subnav-pill uk-margin-remove">
                            <li class="uk-active" data-uk-filter=""><a href="#">All</a></li>
                            <li data-uk-filter="goodwin-nienow"><a href="#">Finance Office</a></li>
                            <li data-uk-filter="strosin groupa"><a href="#">Human Resource Office</a></li>
                            <li data-uk-filter="schamberger plc"><a href="#">Registrars Office </a></li>
                            <li data-uk-filter="schamberger plc"><a href="#">Rectors Office </a></li>
                            <li data-uk-filter="schamberger plc"><a href="#">Vice Rectors Office </a></li>
                      
                        </ul>
                    </div>
                </div>
            </div>
            <div class="uk-width-medium-1-2">
                <label for="contact_list_search">Search... (min 3 char.)</label>
                <input class="md-input" type="text" id="contact_list_search"/>
            </div>
        </div>
    </div>
</div>

<h3 class="heading_b uk-text-center grid_no_results" style="display:none">No results found</h3>

<div class="uk-grid-width-small-1-2 uk-grid-width-medium-1-3 uk-grid-width-large-1-4 uk-grid-width-xlarge-1-5 hierarchical_show" id="contact_list">
    @foreach($data as $index=> $row) 
                                        
    <div data-uk-filter="{{$row->fullName}},{{$row->staffID}}">
        <div class="md-card md-card-hover">
            <div class="md-card-head">
                <div class="md-card-head-menu" data-uk-dropdown="{pos:'bottom-right'}">
                    <i class="md-icon material-icons">&#xE5D4;</i>
                    <div class="uk-dropdown uk-dropdown-small">
                        <ul class="uk-nav">
                            <li><a href="#">Edit</a></li>
                            <li><a href="#">Remove</a></li>
                        </ul>
                    </div>
                </div>
                <div class="uk-text-center">
                    <img class="md-card-head-avatar" src="{!!url('public/albums/staff/'.$row->staffID.'.png')!!}" alt=""/>
                </div>
                <h3 class="md-card-head-text uk-text-center">
                    {{$row->fullName}} <span class="uk-text-truncate"> {{$row->staffID}}</span>
                </h3>
            </div>
            <div class="md-card-content">
                <ul class="md-list">
                    <li>
                        <div class="md-list-content">
                            <span class="md-list-heading">Info</span>
                            <span class="uk-text-small uk-text-muted">Sit mollitia illo totam suscipit laboriosam aliquid veniam incidunt enim sequi qui temporibus.</span>
                        </div>
                    </li>
                    <li>
                        <div class="md-list-content">
                            <span class="md-list-heading">Email</span>
                            <span class="uk-text-small uk-text-muted uk-text-truncate">
                                 {{$row->email}}
                            </span>
                        </div>
                    </li>
                    <li>
                        <div class="md-list-content">
                            <span class="md-list-heading">Phone</span>
                            <span class="uk-text-small uk-text-muted">{{$row->phone}}</span>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
        
    </div>
    
    @endforeach
</div>
<p>&nbsp;</p>
 {!! (new Landish\Pagination\UIKit($data->appends(old())))->render() !!}
    

<div class="md-fab-wrapper">
        <a class="md-fab md-fab-accent" href="#">
            <i class="material-icons">&#xE145;</i>
        </a>
    </div>
@endsection
 
  