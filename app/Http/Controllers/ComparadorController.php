<?php

namespace App\Http\Controllers;

use App\Models\Ordenes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ComparadorController extends Controller
{
    public function index(Request $request)
    {
        // Obtenemos todos los registros del ERP
        $allOrders = collect(Ordenes::obtenerPedidos());

        // Generar lista de proveedores únicos para el filtro (a partir de todos los pedidos)
        $providers = $allOrders->pluck('NOMBRE')->unique()->sort()->values();

        // Aplicar filtros sobre la colección completa
        $ordenes = $allOrders;

        // Filtrar por número de pedido (búsqueda parcial)
        if ($request->filled('pedido')) {
            $searchPedido = $request->pedido;
            $ordenes = $ordenes->filter(function ($order) use ($searchPedido) {
                return strpos((string)$order->PEDIDO, $searchPedido) !== false;
            });
        }

        // Filtrar por proveedor (si se selecciona uno distinto a 'all')
        if ($request->filled('provider') && $request->provider !== 'all') {
            $provider = strtolower(trim($request->provider));
            $ordenes = $ordenes->filter(function ($order) use ($provider) {
                return strtolower(trim($order->NOMBRE)) === $provider;
            });
        }

        // Filtrar por rango de fechas
        if ($request->filled('date_from')) {
            $dateFrom = $request->date_from;
            $ordenes = $ordenes->filter(function ($order) use ($dateFrom) {
                return $order->FECHA_OC >= $dateFrom;
            });
        }
        if ($request->filled('date_to')) {
            $dateTo = $request->date_to;
            $ordenes = $ordenes->filter(function ($order) use ($dateTo) {
                return $order->FECHA_OC <= $dateTo;
            });
        }

        // Obtenemos únicamente pedidos únicos
        $uniqueOrders = $ordenes->unique('PEDIDO')->values();

        // Paginación manual para la colección filtrada
        $page = $request->get('page', 1);
        $perPage = 10; // Puedes ajustar según sea necesario
        $currentPageItems = $uniqueOrders->slice(($page - 1) * $perPage, $perPage)->all();
        $paginatedOrders = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentPageItems,
            $uniqueOrders->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('comparador', compact('paginatedOrders', 'providers'));
    }

    public function comparar(Request $request)
    {
        // Validamos la selección del pedido y la carga del archivo XML
        $request->validate([
            'pedido'   => 'required',
            'xml_file' => 'required|file|mimes:xml'
        ]);

        $pedido = $request->pedido;
        $ordenes = collect(Ordenes::obtenerPedidos());
        $erpProducts = $ordenes->where('PEDIDO', $pedido);

        if ($erpProducts->isEmpty()) {
            return back()->withErrors(['pedido' => 'El pedido seleccionado no existe en el ERP.']);
        }

        // Cabecera del pedido (tomamos el primer registro)
        $erpHeader = $erpProducts->first();

        // Procesamos el archivo XML
        $xmlContent = file_get_contents($request->file('xml_file')->getRealPath());
        $xml = simplexml_load_string($xmlContent, "SimpleXMLElement", LIBXML_NOCDATA);
        // Removemos los namespaces para acceder a la estructura sin prefijos
        $xmlNoNS = $this->removeNamespaces($xml);
        $json = json_encode($xmlNoNS);
        $xmlArray = json_decode($json, true);

        if (!isset($xmlArray['Conceptos']['Concepto'])) {
            return back()->withErrors(['xml_file' => 'El XML no tiene la estructura esperada.']);
        }
        
        $xmlConceptos = $xmlArray['Conceptos']['Concepto'];
        if (isset($xmlConceptos['@attributes'])) {
            $xmlConceptos = [$xmlConceptos];
        }
    
        // Reorganizamos los productos del ERP usando COD_PRO (acumulando pendientes e importes)
        $erpProductsAssoc = [];
        $totalErpProducts = 0;
        $uniqueErpProducts = 0;
        foreach ($erpProducts as $prod) {
            $key = trim((string)$prod->COD_PRO);
            if (isset($erpProductsAssoc[$key])) {
                // Se acumulan los pendientes, ya que es el valor que se utilizará para la comparación
                $erpProductsAssoc[$key]->PENDIENTES += $prod->PENDIENTES;
                $erpProductsAssoc[$key]->IMP_TOTAL_PROD += $prod->IMP_TOTAL_PROD;
            } else {
                $erpProductsAssoc[$key] = $prod;
                $uniqueErpProducts++;
            }
            $totalErpProducts += $prod->PENDIENTES;
        }
    
        // Reorganizamos los productos del XML usando NoIdentificacion (acumulando cantidades e importes)
        $xmlProductsAssoc = [];
        $totalXmlProducts = 0;
        $uniqueXmlProducts = 0;
        foreach ($xmlConceptos as $concepto) {
            $attributes = isset($concepto['@attributes']) ? $concepto['@attributes'] : $concepto;
            $key = isset($attributes['NoIdentificacion']) ? trim($attributes['NoIdentificacion']) : null;
            if ($key) {
                // En el XML se mantiene el campo "Cantidad" ya que se asume que es la cantidad solicitada
                $cantidad = isset($attributes['Cantidad']) ? (float)$attributes['Cantidad'] : 0;
                $importe = isset($attributes['Importe']) ? (float)$attributes['Importe'] : 0;
                $valorUnitario = isset($attributes['ValorUnitario']) ? (float)$attributes['ValorUnitario'] : 0;
                
                // Si el producto tiene el atributo "Descuento", aplicamos el descuento
                if (isset($attributes['Descuento'])) {
                    $descuento = (float)$attributes['Descuento'];
                    $importe = $importe - $descuento;
                    $valorUnitario = $cantidad > 0 ? $importe / $cantidad : 0;
                }
                
                if (isset($xmlProductsAssoc[$key])) {
                    $xmlProductsAssoc[$key]['Cantidad'] += $cantidad;
                    $xmlProductsAssoc[$key]['Importe'] += $importe;
                    $xmlProductsAssoc[$key]['ValorUnitario'] = $xmlProductsAssoc[$key]['Cantidad'] > 0
                        ? $xmlProductsAssoc[$key]['Importe'] / $xmlProductsAssoc[$key]['Cantidad']
                        : 0;
                } else {
                    $attributes['Cantidad'] = $cantidad;
                    $attributes['Importe'] = $importe;
                    $attributes['ValorUnitario'] = $valorUnitario;
                    $xmlProductsAssoc[$key] = $attributes;
                    $uniqueXmlProducts++;
                }
                $totalXmlProducts += $cantidad;
            }
        }
    
        // Creamos un arreglo combinado (unión de claves de ERP y XML)
        $allKeys = array_unique(array_merge(array_keys($erpProductsAssoc), array_keys($xmlProductsAssoc)));
        sort($allKeys);
        $combinedProducts = [];
        foreach ($allKeys as $key) {
            $combinedProducts[] = [
                'key'          => $key,
                'erp_quantity' => isset($erpProductsAssoc[$key]) ? (float)$erpProductsAssoc[$key]->PENDIENTES : null,
                'erp_total'    => isset($erpProductsAssoc[$key]) ? (float)$erpProductsAssoc[$key]->IMP_TOTAL_PROD : null,
                'erp_unit'     => isset($erpProductsAssoc[$key]) ? (float)$erpProductsAssoc[$key]->IMP_UNI : null,
                'xml_quantity' => isset($xmlProductsAssoc[$key]) ? (float)$xmlProductsAssoc[$key]['Cantidad'] : null,
                'xml_total'    => isset($xmlProductsAssoc[$key]) ? (float)$xmlProductsAssoc[$key]['Importe'] : null,
                'xml_unit'     => isset($xmlProductsAssoc[$key]) ? (float)$xmlProductsAssoc[$key]['ValorUnitario'] : null,
            ];
        }
           
        // Identificamos discrepancias en las cantidades (opcional)
        $erpKeys = array_keys($erpProductsAssoc);
        $xmlKeys = array_keys($xmlProductsAssoc);
        $missingInXml = array_diff($erpKeys, $xmlKeys); // Productos que están en ERP pero no en XML
        $extraInXml   = array_diff($xmlKeys, $erpKeys);   // Productos que están en XML pero no en ERP
        $quantityDifferences = [];
        foreach ($erpProductsAssoc as $key => $erpProd) {
            if (isset($xmlProductsAssoc[$key])) {
                // Usamos "Pendientes" del ERP para la comparación
                $erpQuantity = (float)$erpProd->PENDIENTES;
                $xmlQuantity = (float)$xmlProductsAssoc[$key]['Cantidad'];
                if ($erpQuantity !== $xmlQuantity) {
                    $quantityDifferences[$key] = [
                        'erp_quantity' => $erpQuantity,
                        'xml_quantity' => $xmlQuantity,
                    ];
                }
            }
        }
    
        return view('comparador_resultado', compact(
            'erpHeader', 
            'combinedProducts',
            'missingInXml', 
            'extraInXml', 
            'quantityDifferences',
            'totalErpProducts',
            'totalXmlProducts',
            'uniqueErpProducts',
            'uniqueXmlProducts'
        ));
    }
    
    private function removeNamespaces($xml)
    {
        $xmlString = $xml->asXML();
        // Elimina los prefijos (por ejemplo, "cfdi:")
        $xmlString = preg_replace('/(<\/?)(\w+):/', '$1', $xmlString);
        return simplexml_load_string($xmlString, 'SimpleXMLElement', LIBXML_NOCDATA);
    }
}
