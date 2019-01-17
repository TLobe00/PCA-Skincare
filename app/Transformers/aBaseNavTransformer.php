<?php


namespace App\Transformers;


use League\Fractal\TransformerAbstract;

/**
 * Class aBaseCustomTransformer
 *
 * All transformers extending this class will be mapped to a single level array.
 *
 * @package App\Transformers
 */
abstract class aBaseNavTransformer extends TransformerAbstract {
    /**
     * @var array
     */
    protected $map = [];

    /**
     * @var bool
     */
    protected $formatKeys = false;

    /**
     * @var array
     */
    protected $ignoreFormatting = [];

    /**
     * @param array $payload
     * @return array
     */
    protected function mapData($payload = []): array {
        $data = [];

        foreach ($this->map as $k => $v) {
            if (is_null($v)) {
                continue;
            }

            if(is_array($v)) {
                foreach($v as $_k => $_v) {
                    $value = null;
                    $_k = $k . $_k;
                    $method = "handle" . studly_case($_k);

                    // Handle fetching data
                    $value = $this->resolveValue($method, $payload, $_k);

                    // Handle formatting key
                    if($rV = $this->resolveKey($_k, $_v)) {
                        $_v = $rV;
                    }

                    $data[$_v] = $value;
                }
            } else {
                $value = null;
                $method = "handle" . studly_case($k);

                // Handle fetching data
                $value = $this->resolveValue($method, $payload, $k);

                // Handle formatting key
                if($rV = $this->resolveKey($k, $v)) {
                    $v = $rV;
                }

                $data[$v] = $value;
            }
        }

        return $data;
    }

    /**
     * @param $method
     * @param $payload
     * @param $key
     * @return mixed|null
     */
    private function resolveValue($method, $payload, $key) {
        // Handle fetching data
        if (method_exists($this, $method)) {
            $value = call_user_func_array([$this, $method], [$payload]);
        } else {
            $value = isset($payload[$key]) ? $payload[$key] : null;
        }

        return $value;
    }

    /**
     * @param $key
     * @param $mappedKey
     * @return null
     */
    private function resolveKey($key, $mappedKey) {
        $k = null;

        // Handle formatting key
        if($this->formatKeys && method_exists($this, 'keyFormatter') && !in_array($key, $this->ignoreFormatting)) {
            $k = $this->keyFormatter($mappedKey);
        }

        return $k;
    }
}