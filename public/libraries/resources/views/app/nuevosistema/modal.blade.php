<div class="profile-edit-container">
        <div class="custom-form">
<div class="main-register-wrap {{$name}}">
    <div class="main-overlay"></div>
    <div class="main-register-holder">
        <div class="main-register fl-wrap">
            <div class="close-reg"><i class="fa fa-times"></i></div>
            <h3 id="modal_titulo_{{$name}}"></h3>
            <div class="mx-modal-cuerpo" id="modal_cuerpo_{{$name}}"></div>
        </div>
    </div>
</div>
</div>
</div>
<script>
@if(isset($screen))
modal({click:'#{{$name}}',screen:'{{$screen}}'});
@else
modal({click:'#{{$name}}'});
@endif
</script>