<br>
<table width="100%" style="text-align:center; background-color:#e0e0e0;"> 
    <tr>
        <td>
                        <table style="width:720px; max-width:90%; margin:0 auto; font-size:18px; text-align:left; background-color:#e0e0e0;">
                <tr>
                    <td colspan="2" style="text-align:center; padding-bottom:6px;">
                                    <h3 style="margin:0 0 6px 0; font-size:26px;">Comprobante de pago electrónico</h3>
                    </td>
                </tr>
                <tr>
                    <td style="width:50%; vertical-align:top; padding-right:10px; text-align:left;">
                        <p style="margin:0;"><strong>Periodo:</strong> @if(isset($period->settlement_start_date) && isset($period->settlement_end_date)) {{$period->settlement_start_date}} - {{$period->settlement_end_date}} @elseif(isset($period->issue_date)) {{$period->issue_date}} @else - @endif</p>
                        <p style="margin:0;"><strong>Comprobante de pago No.:</strong> {{$resolution->prefix ?? ''}} - {{$request->consecutive ?? ''}}</p>
                    </td>
                    <td style="width:50%; vertical-align:top; padding-left:10px; text-align:left;">
                        <p style="margin:0;"><strong>NIT:</strong> {{$company->identification_number ?? ''}}-{{$company->dv ?? ''}}</p>
                        <p style="margin:0;"><strong>Doc. Ident.:</strong> @if(isset($request->nombretipodocid)) {{$request->nombretipodocid}} @elseif(isset($request->type_document_id)) {{$request->type_document_id}} @else - @endif</p>
                    </td>
                </tr>
                <br>
                <tr>
                    <td style="width:100%; vertical-align:top; padding-right:10px; text-align:left;">
                        @if($request->type_document_id == 9)
                            <p style="margin:0; font-weight:700;"><strong>Nómina: DOCUMENTO SOPORTE DE PAGO DE NOMINA ELECTRONICA</strong></p>
                        @else
                            @if($request->type_note == 1)
                                <p style="margin:0; font-weight:700;"><strong>Nómina: DOCUMENTO SOPORTE DE NOMINA ELECTRONICA DE AJUSTE - REEMPLAZAR</strong></p>
                            @else
                                <p style="margin:0; font-weight:700;"><strong>Nómina: DOCUMENTO SOPORTE DE NOMINA ELECTRONICA DE AJUSTE - ELIMINAR</strong></p>
                            @endif
                        @endif
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<br>
<hr style="border:0; height:2px; background-color:#000; margin:8px 0 12px 0;">