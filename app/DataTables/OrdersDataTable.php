<?php

namespace App\DataTables;

use App\Models\Order;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class OrdersDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
        ->addColumn('#', function ($row) {
            // The serial number will be rendered on the client-side
            static $index = 0; // Static variable to maintain the index across multiple rows
            return ++$index + $this->request->get('start');
        })
        ->addColumn('products', function (Order $order) {
            
            return $order->products->map(function ($product) {
                static $index = 0;
                return ++$index + $this->request->get('start'). ' - '.$product->productType . ' , ' . $product->brand. ' , ' . $product->weightOfBag. ' , ' . $product->numberOfContainers;
            })->implode(' <br>'); // Customize the display of products
        })
        ->addColumn('action', function( $data){
            $id= $data->id;
           return "<div>
           <a id='editProductBtn' type='button' data-id='$id'   class='text-primary'><i class='fas fa-edit'></i></a>
           <a id='deleteBtn' data-id='$id'  class='text-danger'><i class='fas fa-trash'></i></a>
           </div>";
          })
        ->rawColumns(['#','action','products']); // Allow HTML rendering in the 'products' column

    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Order $model, Order $orderNo): QueryBuilder
    {
        return $model->newQuery()->with('products')
        ->select('orders.id', 'orders.order_no') // Select order_no along with other fields
        ->leftJoin('order_products', 'orders.id', '=', 'order_products.order_id')
        ->groupBy('orders.id', 'orders.order_no');

    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
        ->setTableId('orders-table')
        ->columns($this->getColumns())
        ->parameters(['ordering' => false,])
        ->minifiedAjax()
        //->dom('Bfrtip')
        ->orderBy(1)
        ->selectStyleSingle()
        ->scrollY(false)
        ->scrollX(false)
        ->autoWidth(true)
        
        ->buttons([
            Button::make('excel'),
            Button::make('csv'),
            Button::make('pdf'),
            Button::make('print'),
            Button::make('reset'),
            Button::make('reload')
        ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            ['data' => '#', 'name' => '#', 'title' => '#'],
            Column::make('order_no'),
            Column::make('products'),
            Column::computed('action')
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Orders_' . date('YmdHis');
    }
}
