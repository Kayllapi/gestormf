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
<div class="row">
  <div class="col-12">
		<ul class="paginator">
     @if ($results->onFirstPage())
      <li class="paginator__item paginator__item--prev">
        <a href="javascript:;" class="prevposts-link disabled">
          <i class="icon ion-ios-arrow-back"></i>
        </a>
      </li>
      @else
      <li class="paginator__item paginator__item--prev">
        <a href="javascript:;" href="{{$results->url(1)}}{{$addurl}}" class="prevposts-link">
          <i class="icon ion-ios-arrow-back"></i>
        </a>
      </li>
    @endif   
        @foreach ($elements as $element)
            @if (is_string($element))
             <li class="paginator__item">
                <a href="javascript:;" class="disabled">{{ $element }}</a>
            </li>
            @endif
            @if (is_array($element))
                @foreach ($element as $page => $urlpage)
                    @if ($page == $results->currentPage())
                     <li class="paginator__item paginator__item--active">
                        <a href="javascript:;" class="current-page">{{ $page }}</a>
                    </li>
                    @else
                    <li class="paginator__item">
                        <a href="{{$results->url($page)}}{{$addurl}}">{{ $page }}</a>
                    </li>
                    @endif
                @endforeach
            @endif
        @endforeach
     @if ($results->hasMorePages())
            <li class="paginator__item paginator__item--next">
              <a href="javascript:;" href="{{$results->url($page)}}{{$addurl}}" class="nextposts-link">
                <i class="icon ion-ios-arrow-forward"></i>
              </a>
            </li>
     @else
            <li class="paginator__item paginator__item--next">
             <a href="javascript:;" class="nextposts-link disabled">
               <i class="icon ion-ios-arrow-forward"></i>
             </a>
            </li>
     @endif
    </ul>
		</div>
</div>
@endif 
 
