<?php

namespace App\Http\Helpers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CSVExport
{
    private $table;
    private array $mergeFields;

    public function __construct(Model $model)
    {
        $this->table = $model->getTable();
    }

    public function merge(array $fields)
    {
        $this->mergeFields = $fields;
        return $this;
    }

    public function export(string $filename)
    {
        $filename = "$filename.csv";
        $fp = fopen('php://output', 'w');

        $columns = collect(DB::select("SHOW COLUMNS FROM " . $this->table));

        $header = $columns->map(function ($column) {
            if (!in_array($column->Field, ['id', 'created_at', 'updated_at'])) {
                return $column->Field;
            }
        })->filter(function ($column) {
            return $column != null;
        })->toArray();

        if(!empty($this->mergeFields)){
            $header = array_merge($header, $this->mergeFields);
        }

        header('Content-type: application/csv');
        header('Content-Description: File Transfer');
        header('Content-Disposition: attachment; filename=' . $filename);
        fputcsv($fp, $header);
        fclose($fp);
        exit;
    }
}
