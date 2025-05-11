@extends('layouts.app')

@section('title', 'Panel Hotel – Comisiones')

@section('content')
  <h1 class="h3 mb-4 fw-bold">Comisiones del hotel</h1>

  <table class="table table-striped table-bordered shadow-sm">
    <thead class="table-light">
    <tr>
      <th>Mes</th>
      <th>Traslados</th>
      <th>Comisión&nbsp;€</th>
    </tr>
    </thead>
    <tbody>
    @forelse($stats as $row)
    <tr>
      <td>{{ \Carbon\Carbon::parse($row->mes . '-01')->isoFormat('MMMM YYYY') }}</td>
      <td>{{ $row->traslados }}</td>
      <td>{{ number_format($row->total_comision, 2) }} €</td>
    </tr>
    @empty
    <tr>
      <td colspan="3" class="text-center text-muted">Aún sin reservas</td>
    </tr>
    @endforelse
    </tbody>

  </table>
@endsection
