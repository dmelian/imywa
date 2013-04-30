select transaccion.transaccion, transaccion.cuenta, transaccion.importe as importe
	, if(transaccion.tipo = 'debe', if(sum(liqdebe.importe) is null, 0 ,-sum(liqdebe.importe)), if(sum(liqhaber.importe) is null, 0, sum(liqhaber.importe))) as importeliquidado
from liquidacion as liqdebe 
	right join transaccion on liqdebe.transacciondebe = transaccion.transaccion 
	left join liquidacion as liqhaber on transaccion.transaccion = liqhaber.transaccionhaber
group by transaccion.transaccion, transaccion.cuenta
;

select transaccion.transaccion, transaccion.cuenta, transaccion.importe as importe
	, transaccion.importe + if(transaccion.tipo = 'debe', if(sum(liqdebe.importe) is null,0,-sum(liqdebe.importe)), if(sum(liqhaber.importe) is null, 0, sum(liqhaber.importe))) as importependiente
from liquidacion as liqdebe 
	right join transaccion on liqdebe.transacciondebe = transaccion.transaccion 
	left join liquidacion as liqhaber on transaccion.transaccion = liqhaber.transaccionhaber
group by transaccion.transaccion, transaccion.cuenta
;

select transaccion.cuenta
	, sum(transaccion.importe) as saldopte
	, sum(if(transaccion.tipo = 'debe', transaccion.importe, 0)) as saldodeudor
	, sum(if(transaccion.tipo = 'haber', transaccion.importe, 0)) as saldoacreedor
	, if(sum(liqdebe.importe) is null, 0, sum(liqdebe.importe)) as saldodeudorliquidado 
	, if(sum(liqhaber.importe) is null, 0, -sum(liqhaber.importe)) as saldoacreedorliquidado
from liquidacion as liqdebe 
	right join transaccion on liqdebe.transacciondebe = transaccion.transaccion 
	left join liquidacion as liqhaber on transaccion.transaccion = liqhaber.transaccionhaber
group by transaccion.cuenta
;

select transaccion.cuenta
	, sum(transaccion.importe) as saldo
	, sum(if(transaccion.tipo = 'debe', transaccion.importe, 0)) 
		+ if(sum(liqdebe.importe) is null, 0, -sum(liqdebe.importe)) as saldodeudorpteliquidar 
	, sum(if(transaccion.tipo = 'haber', transaccion.importe, 0)) 
		+ if(sum(liqhaber.importe) is null, 0, sum(liqhaber.importe)) as saldoacreedorpteliquidar
from liquidacion as liqdebe 
	right join transaccion on liqdebe.transacciondebe = transaccion.transaccion 
	left join liquidacion as liqhaber on transaccion.transaccion = liqhaber.transaccionhaber
group by transaccion.cuenta
;

