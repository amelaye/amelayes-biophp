<?php
/**
 * MicroarrayAnalysisAdaptive
 * @author Amélie DUVERNET akka Amelaye
 * Inspired by BioPHP's project biophp.org
 * Created 26 february 2019
 * Last modified 26 february 2019
 */
namespace MinitoolsBundle\Service;

class MicroarrayAnalysisAdaptive
{
    /**
     * @param $file
     * @return mixed
     * @throws \Exception
     */
    public function processMicroarrayDataAdaptiveQuantificationMethod($file)
    {
        try {
            $data_array2 = [];
            $data_array3 = [];
            $data_array4 = [];
            $n_data = 0;

            // find data for first column and row, and remove all headings;
            $file = substr($file,strpos($file,"1\t1\t"));
            // remove from file returns (\r) and (\")
            $file = preg_replace("/\r|\"/","",$file);

            // split file into lines ($data_array)
            $data_array = preg_split("/\n/",$file, -1, PREG_SPLIT_NO_EMPTY);

            // compute data-background (save result in $data_array2) and
            //   sum of all data-background ($sum_ch1 and $sum_ch2)
            $sum_ch1 = 0;
            $sum_ch2 = 0;
            foreach($data_array as $key => $val) {
                // example of line to be splitted:
                // 1        2        G16        1136        159        538        118
                // where        1 and 2 define position in the plate
                //              G16 is name of gene/experiment
                //              1136 is reading of chanel 1, and 159 is the background
                //              538 is reading of chanel 2, and 159 is the background
                $line_element = preg_split("/\t/",$val, -1, PREG_SPLIT_NO_EMPTY);
                if (sizeof ($line_element) < 7) {
                    continue;
                }

                // This is the name of the gene studied
                $name = $line_element[2];

                // For chanel 1
                // calculate data obtained in chanel 1 minus background
                $ch1_bg = $line_element[3] - $line_element[4];
                // save data to a element in $data_array2 (separate diferent calculations from the same gene with commas)
                $data_array2[$name][1] .= ",".$ch1_bg;
                $sum_ch1 += $ch1_bg; // $sum_ch1 will record the sum of all (chanel 1 - background) values

                // For chanel 2
                // calculate data obtained in chanel 2 minus background
                $ch2_bg = $line_element[5] - $line_element[6];
                // save data to a element in $data_array2 (separate diferent calculations from the same gene with commas)
                $data_array2[$name][2] .= ",".$ch2_bg;
                $sum_ch2 += $ch2_bg; // $sum_ch1 will record the sum of all (chanel 2 - background) values
                $n_data ++; // count number of total elements
            }

            // Compute (data-background)*100/sum(data-background)),
            //    where sum(data-background) is $sum_ch1 or $sum_ch2
            //    and save data in  $data_array3
            foreach($data_array2 as $key => $val) {
                // split data separated by comma (chanel 1)
                $data_element = preg_split("/,/",$data_array2[$key][1], -1, PREG_SPLIT_NO_EMPTY);
                foreach($data_element as $key2 => $value) {
                    $ratio = $value * 100 / $sum_ch1; // compute ratios
                    $data_array3[$key][1] .= ",$ratio"; // save result to $data_array3
                }

                // split data separated by comma (chanel 2)
                $data_element = preg_split("/,/",$data_array2[$key][2], -1, PREG_SPLIT_NO_EMPTY);
                foreach($data_element as $key2 => $value) {
                    $ratio = $value * 100 / $sum_ch2; // compute ratios
                    $data_array3[$key][2] .= ",$ratio"; // save result to $data_array3
                }
            }

            // Compute ratios for values in chanel 1 and chanel 2
            //     chanel 1/chanel 2  and  chanel 2/chanel 1
            // save results to $data_array4
            foreach($data_array3 as $key => $val) {
                $data_element1 = preg_split("/,/",$data_array3[$key][1], -1, PREG_SPLIT_NO_EMPTY);
                $data_element2 = preg_split("/,/",$data_array3[$key][2], -1, PREG_SPLIT_NO_EMPTY);
                foreach ($data_element1 as $key2 => $value) {
                    $ratio = $data_element1[$key2] / $data_element2[$key2]; //compute ch1/ch2
                    $data_array4[$key][1] .= ",$ratio"; // and save
                    $ratio = $data_element2[$key2] / $data_element1[$key2]; //compute ch2/ch1
                    $data_array4[$key][2] .= ",$ratio"; // and save
                }
            }

            ksort($data_array4);

            foreach($data_array4 as $key => $val) {
                $results[$key]["n_data"] = substr_count($data_array4[$key][1],",");
                $results[$key]["median1"] = median($data_array4[$key][1]);
                $results[$key]["median2"] = median($data_array4[$key][2]);

            }
            return $results;
        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }


    /**
     * @param $cadena
     * @return float|int
     * @throws \Exception
     */
    public function mean($cadena)
    {
        try {
            $data = preg_split("/,/",$cadena,-1,PREG_SPLIT_NO_EMPTY);
            $sum = 0;
            $numValidElements = 0;

            foreach($data as $key => $val) {
                if(isset($val)) {
                    $sum += $val;
                    $numValidElements += 1;
                }
            }
            $mean = $sum / $numValidElements;
            $mean = round ($mean,3);
            return $mean;
        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }


    /**
     * @param $cadena
     * @return float|int
     * @throws \Exception
     */
    function median($cadena)
    {
        try {
            $data = preg_split("/,/",$cadena,-1,PREG_SPLIT_NO_EMPTY);
            sort($data);
            $i = floor(sizeof($data)/2);
            if (sizeof($data) / 2 != $i) {
                return $data[$i];
            }
            return($data[$i-1] + $data[$i])/2;
        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }


    /**
     * @param $cadena
     * @return float|int
     * @throws \Exception
     */
    function variance($cadena)
    {
        try {
            $mean = $this->mean($cadena);
            $data = preg_split("/,/",$cadena,-1,PREG_SPLIT_NO_EMPTY);
            $sum = 0;
            $numValidElements = 0;

            foreach($data as $key => $val) {
                if(isset($val)) {
                    $tmp = $val - $mean;
                    $sum += $tmp * $tmp;
                    $numValidElements += 1;
                }
            }

            $variance = $sum / ( $numValidElements - 1 );
            $variance = round($variance,3);
            return $variance;
        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }
}