<?php 
$url = Request::fullUrl(); 
$counturl = explode('?',$url);
$addurl = '';
if(isset($_GET['page'])){
    $addurl = str_replace('&page='.$_GET['page'],'',$counturl[1]);
    $addurl = '&'.str_replace('page='.$_GET['page'],'',$addurl);
}else{
    if(count($counturl)>1){
        $addurl = '&'.$counturl[1];
    }
}
?>
@if ($results->hasPages())
<div class="pagination">
     @if ($results->onFirstPage())
     <a href="javascript:;" class="prevposts-link disabled"><b>‹‹</b></a>
     @else
     <a href="javascript:;" href="{{$results->url(1)}}{{$addurl}}" class="prevposts-link"><b>‹‹</b></a>
     @endif
    
        @foreach ($elements as $element)
            @if (is_string($element))
                <a href="javascript:;" class="disabled">{{ $element }}</a>
            @endif
            @if (is_array($element))
                @foreach ($element as $page => $urlpage)
                    @if ($page == $results->currentPage())
                        <a href="javascript:;" class="current-page">{{ $page }}</a>
                    @else
                        <a href="{{$results->url($page)}}{{$addurl}}">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach
     @if ($results->hasMorePages())
            <a href="javascript:;" href="{{$results->url($page)}}{{$addurl}}" class="nextposts-link"><b>››</b></a>
     @else
            <a href="javascript:;" class="nextposts-link disabled"><b>››</b></a>
     @endif
 </div> 
@endif 
