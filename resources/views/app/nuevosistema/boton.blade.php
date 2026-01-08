<a href="javascript:;"
   onclick="{{$onclick}},click_sonido()" 
   <?php echo isset($id) ? 'id="'.$id.'"' : '' ?>
   @if(isset($attr))
   @foreach($attr as $key => $value)
   {{$key}}="{{$value}}" 
   @endforeach
   @endif
   class="category-bubble <?php echo isset($class)?$class:'' ?>">
    <img src="{{$imagen}}" 
         class="category-bubble-icon">
    <h2 class="category-bubble-title">{{$nombre}}</h2>
</a>