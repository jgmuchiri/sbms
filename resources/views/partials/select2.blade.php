@push('styles')
<link href="/plugins/select2/select2.css" rel="stylesheet" type="text/css"/>
@endpush

@push('scripts')

<script src="/plugins/select2/select2.min.js" type="text/javascript"></script>
<script>
    jQuery(document).ready(function () {

        @if(is_array($select2))

                @foreach($select2 as $sel)

                        $('{{$sel}}').removeClass('form-control');

                    @if(isset($select2_opts))
                    $("{{$sel}}").select2({{$select2_opts}});
                    @else
                    $("{{$sel}}").select2();
                    @endif
                 @endforeach

        @else


        $('{{$select2}}').removeClass('form-control');

        @if(isset($select2_opts))
            $("{{$select2}}").select2({{$select2_opts}});
                @else
            $("{{$select2}}").select2();
        @endif

        @endif
    });
</script>
@endpush