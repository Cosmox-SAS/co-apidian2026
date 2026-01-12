<footer id="footer">
    <hr style="margin-bottom: 2px;">
    <p id='mi-texto' style="font-size: 5px">Equivalent Electronic Receipt No: {{$resolution->prefix}} - {{$request->number}} - Generation Date and Time: {{$date}} - {{$time}}</p>
    <p style="font-size: 4px">CUDE:{{$cufecude}}</p>
    @isset($request->foot_note)
        <p id='mi-texto-1' style="font-size: 5px">{{$request->foot_note}}</p>
    @endisset
</footer>
