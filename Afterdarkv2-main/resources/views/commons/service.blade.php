@if(isset($error))
    <script type="text/javascript">
        var opener = window.opener;
        if(opener) {
            opener.Connect.thirdParty.error("{{ $provider }}");
        }
        window.close();
    </script>
@else
    <script type="text/javascript">
        var opener = window.opener;
        if(opener) {
            opener.Connect.thirdParty.callback({!! json_encode($service) !!}, "{{ $provider }}");
        }
        window.close();
    </script>
@endif