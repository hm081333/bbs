<?php

namespace App\Utils;

use App\Exceptions\Server\Exception;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use function response;

/**
 * Excel表格辅助类
 * Class ExcelHelper
 */
class ExcelHelper
{
    /**
     * 读取表格
     * @param string $filename  文件路径
     * @param int    $begin_row 开始行
     * @param array  $assoc     关联数组
     * @return array
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function readSheet(string $filename, int $begin_row = 1, array $assoc = [])
    {
        $objPHPExcel = IOFactory::load($filename);
        // 打开第一个表
        $sheet = $objPHPExcel->getSheet(0);
        // 获取最后行与最后列
        $highest = $sheet->getHighestRowAndColumn();
        // 列编号转索引数字
        $highest['column'] = count($assoc) ?: Coordinate::columnIndexFromString($highest['column']);
        $table = [];
        // 行循环
        for ($row = $begin_row; $row <= $highest['row']; $row++) {
            $line = [];
            // 列循环，A对应的是1，所以从1开始
            for ($column = 1; $column <= $highest['column']; $column++) {
                $line[] = $sheet->getCellByColumnAndRow($column, $row)->getValue();
            }
            // 过滤空行
            if (empty(array_filter($line, function ($item) {
                return !empty($item);
            }))) {
                continue;
            }

            $table[] = !$assoc ? $line : array_combine($assoc, $line);
        }
        return $table;
    }

    /**
     * 生成表格 返回写入器
     * @param array $header     表头数组
     * @param array $sheet_data 表格数据数组
     * @return Xlsx
     * @throws Exception
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function writeSheet(array $header, array $sheet_data)
    {
        if (empty($header)) throw new Exception('参数错误');
        // 实例化 Spreadsheet 对象
        $spreadsheet = new Spreadsheet();

        // 获取活动工作薄
        $sheet = $spreadsheet->getActiveSheet();

        // 计算最后行与最后列
        $highest = [
            // 加1为表头
            'row' => count($sheet_data) + 1,
            // 列根据表头定义
            'column' => count($header),
        ];
        // 列对应数据的数组下标
        $column_keys = array_keys($header);
        for ($row = 1; $row <= $highest['row']; $row++) {
            $data = $row == 1 ? $header : $sheet_data[$row - 2];
            for ($column = 1; $column <= $highest['column']; $column++) {
                // 根据行与列获取单元格
                $cell = $sheet->getCellByColumnAndRow($column, $row);
                // 设置单元格数据
                $cell->setValue($data[$column_keys[$column - 1]]);
                $cell_style = $cell->getStyle();
                // 设置单元格格式 文本格式
                $cell_style->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
                /**
                 * 单元格文字样式设置
                 */
                // getStyle 获取单元格样式
                // getFont 获取单元格文字样式
                // setBold 设置文字粗细
                // setName 设置文字字体
                // setSize 设置文字大小
                // $cell_style->getFont()->setBold(true)->setName('宋体')->setSize(20);
                /**
                 * 单元格文字颜色
                 */
                // getColor 获取坐标颜色
                // setRGB 设置字体颜色
                // getRGB 获取字体颜色
                // setARGB 设置字体颜色
                // getARGB 获取字体颜色
                // $cell_style->getFont()->getColor()->setRGB('#AEEEEE');
                // $cell_style->getFont()->getColor()->setARGB('FFFF0000');
            }
        }

        // Xlsx类型，返回写入器
        return new Xlsx($spreadsheet);
    }

    /**
     * 生成表格并返回下载
     * @param array  $header
     * @param array  $sheet_data
     * @param string $filename
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function writeSheetAndDownload(array $header, array $sheet_data, string $filename = '')
    {
        return response()->streamDownload(function () use ($header, $sheet_data) {
            $this->writeSheet($header, $sheet_data)->save('php://output');
        }, $filename);
    }

    /**
     * 生成表格并保存
     * @param array  $header
     * @param array  $sheet_data
     * @param string $filename
     * @return void
     * @throws Exception
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function writeSheetAndSave(array $header, array $sheet_data, string $filename)
    {
        $this->writeSheet($header, $sheet_data)->save($filename);
    }
}
