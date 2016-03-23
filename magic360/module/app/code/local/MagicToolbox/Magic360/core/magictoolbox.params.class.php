<?php

@ini_set('memory_limit', '512M');

if(!function_exists('lcfirst')) {
    function lcfirst($str) {
        $str[0] = strtolower($str[0]);
        return $str;
    }
}

if(!function_exists('htmlspecialchars_decode')) {
    function htmlspecialchars_decode($string,$style=ENT_COMPAT) {
        $translation = array_flip(get_html_translation_table(HTML_SPECIALCHARS,$style));
        if($style === ENT_QUOTES){ $translation['&#039;'] = '\''; }
        return strtr($string,$translation);
    }
}

if(!function_exists('array_combine')) {
    function array_combine($arr1, $arr2) {
        $out = array();

        $arr1 = array_values($arr1);
        $arr2 = array_values($arr2);

        foreach($arr1 as $key1 => $value1) {
            $out[(string)$value1] = $arr2[$key1];
        }

        return $out;
    }
}

if(!function_exists('file_put_contents')) {
    function file_put_contents($filename, $data) {
        $fp = fopen($filename, 'w+');
        if ($fp) {
            fwrite($fp, $data);
            fclose($fp);
        }
    }
}

if(!defined('MagicToolboxParamsClassLoaded')) {

    define('MagicToolboxParamsClassLoaded', true);

    /**
     * MagicToolboxParamsClass
     *
     */
    class MagicToolboxParamsClass {

        /**
         * Options
         *
         * @var   array
         *
         */
        var $params = array();

        /**
         * General profile
         *
         * @var   string
         *
         */
        var $generalProfile = 'default';

        /**
         * Current profile
         *
         * @var   string
         *
         */
        var $currentProfile = '';

        /**
         * Scope
         *
         * @var   string
         *
         */
        var $scope = 'tool';

        /**
         * Mapping array
         *
         * @var   array
         *
         */
        var $mapping = array();

        /**
         * Constructor
         *
         * @return void
         */
        function MagicToolboxParamsClass() {
            $this->params = array($this->generalProfile => array());
            $this->currentProfile = $this->generalProfile;
        }

        /**
         * Method to get scope
         *
         * @return string
         */
        function getScope() {
            return $this->scope;
        }

        /**
         * Method to set scope
         *
         * @param string $scope Scope
         *
         * @return void
         */
        function setScope($scope) {
            $this->scope = $scope;
        }

        /**
         * Method to get current profile name
         *
         * @return string
         */
        function getProfile() {
            return $this->currentProfile;
        }

        /**
         * Method to get all profile names
         *
         * @return array
         */
        function getProfiles() {
            return array_keys($this->params);
        }

        /**
         * Method to set current profile name
         *
         * @param string $profile Profile name
         *
         * @return boolean
         */
        function setProfile($profile) {
            /*if(!$profile) return false;
            if(!isset($this->params[$profile])) {
                $this->params[$profile] = array();
            }*/
            $this->currentProfile = $profile;
            return true;
        }

        /**
         * Method to rename general profile
         *
         * @param string $profile Profile name
         *
         * @return boolean
         */
        function renameGeneralProfile($profile) {
            if(!$profile) return false;
            if($this->generalProfile != $profile) {
                $this->params[$profile] = $this->params[$this->generalProfile];
                if($this->currentProfile == $this->generalProfile) {
                    $this->currentProfile = $profile;
                }
                unset($this->params[$this->generalProfile]);
                $this->generalProfile = $profile;
            }
            return true;
        }

        /**
         * Method to reset to general profile
         *
         * @return void
         */
        function resetProfile() {
            $this->currentProfile = $this->generalProfile;
        }

        /**
         * Method to delete profile
         *
         * @param string $profile Profile name
         *
         * @return boolean
         */
        function deleteProfile($profile) {
            if(isset($this->params[$profile]) && $profile != $this->generalProfile) {
                if($profile == $this->currentProfile) $this->currentProfile = $this->generalProfile;
                unset($this->params[$profile]);
                return true;
            }
            return false;
        }

        /**
         * Method to check if profile exists
         *
         * @param string $profile Profile name
         *
         * @return boolean
         */
        function profileExists($profile) {
            return isset($this->params[$profile]);
        }

        /**
         * Method to get profile's params
         *
         * @param string $profile Profile name
         *
         * @return array|null
         */
        //NOTE: old name: getArray
        function getParams($profile = '') {
            if(!$profile) $profile = $this->currentProfile;
            return isset($this->params[$profile]) ? $this->params[$profile] : null;
        }

        /**
         * Method to get param names
         *
         * @param string $profile Profile name
         *
         * @return array|null
         */
        function getNames($profile = '') {
            if(!$profile) $profile = $this->currentProfile;
            return isset($this->params[$profile]) ? array_keys($this->params[$profile]) : null;
        }

        /**
         * Method to append params
         *
         * @param array  $params Params to append
         * @param string $profile Profile name
         *
         * @return array
         */
        //NOTE: old name: appendArray
        function appendParams($params, $profile = '') {
            if(!$profile) $profile = $this->currentProfile;
            //NOTE: we can't use array_merge because it overwrites the subarrays, and not merge them
            //$this->params[$profile] = array_merge($this->params[$profile], $params);
            foreach($params as $key => $value) {
                if(array_key_exists($key, $this->params[$profile]) && is_array($this->params[$profile][$key])) {
                    $this->params[$profile][$key] = array_merge($this->params[$profile][$key], $value);
                } else {
                    $this->params[$profile][$key] = $value;
                }
            }
            return $this->params[$profile];
        }

        /**
         * Method to check if param exists
         *
         * @param string  $id      Param ID
         * @param string  $profile Profile name
         * @param boolean $strict  Flag; whether to check the general profile or no
         *
         * @return boolean
         */
        //NOTE: old name: exists
        function paramExists($id, $profile = '', $strict = true) {
            if(!$profile) $profile = $this->currentProfile;
            return isset($this->params[$profile][$id]) || !$strict && isset($this->params[$this->generalProfile][$id]);
        }

        /**
         * Method to remove param
         *
         * @param string  $id      Param ID
         * @param string  $profile Profile name
         *
         * @return void
         */
        function removeParam($id, $profile = '') {
            if(!$profile) $profile = $this->currentProfile;
            if(isset($this->params[$profile][$id])) {
                unset($this->params[$profile][$id]);
            }
        }

        /**
         * Method to get param's data
         *
         * @param string  $id      Param ID
         * @param string  $profile Profile name
         * @param boolean $strict  Flag; whether to check the general profile or no
         *
         * @return mixed|null
         */
        //NOTE: old name: get
        function getParam($id, $profile = '', $strict = true) {
            if(!$profile) $profile = $this->currentProfile;
            return isset($this->params[$profile][$id]) ? $this->params[$profile][$id] : ((!$strict && isset($this->params[$this->generalProfile][$id])) ? $this->params[$this->generalProfile][$id] : null);
        }

        /**
         * Method to set param's value
         *
         * @param string $id      Param ID
         * @param mixed  $value   Param value
         * @param string $profile Profile name
         *
         * @return void
         */
        //NOTE: old name: set
        function setValue($id, $value, $profile = '') {
            if(!$profile) $profile = $this->currentProfile;
            if(isset($this->params[$profile][$id])) {
                $this->params[$profile][$id]['value'] = $value;
            } else if(isset($this->params[$this->generalProfile][$id])) {
                $this->params[$profile][$id] = $this->params[$this->generalProfile][$id];
                $this->params[$profile][$id]['value'] = $value;
            } else {
                $this->params[$profile][$id] = array(
                    'id' => $id,
                    'group' => '',
                    'order' => '',
                    'default' => $value,
                    'label' => '',
                    'description' => '',
                    'type' => 'text',
                    'value' => $value,
                    'scope' => ''
                );
            }
        }

        /**
         * Method to get param's value
         *
         * @param string  $id      Param ID
         * @param string  $profile Profile name
         * @param boolean $strict  Flag; whether to check the general profile or no
         *
         * @return mixed|null
         */
        function getValue($id, $profile = '', $strict = false) {
            if(!$profile) $profile = $this->currentProfile;
            $param = $this->getParam($id, $profile, $strict);
            if($param) {
                return isset($param['value']) ? $param['value'] : $param['default'];
            }
            return null;
        }

        /**
         * Method to get param's default value
         *
         * @param string  $id      Param ID
         * @param string  $profile Profile name
         * @param boolean $strict  Flag; whether to check the general profile or no
         *
         * @return mixed|null
         */
        function getDefaultValue($id, $profile = '', $strict = false) {
            if(!$profile) $profile = $this->currentProfile;
            $param = $this->getParam($id, $profile, $strict);
            return $param ? $param['default'] : null;
        }

        /**
         * Method to get param's values
         *
         * @param string  $id      Param ID
         * @param string  $profile Profile name
         * @param boolean $strict  Flag; whether to check the general profile or no
         *
         * @return array|null
         */
        function getValues($id, $profile = '', $strict = false) {
            if(!$profile) $profile = $this->currentProfile;
            $param = $this->getParam($id, $profile, $strict);
            if($param) {
                return isset($param['values']) ? $param['values'] : array($param['default']);
            } else return null;
        }

        /**
         * Method to check if values exists
         *
         * @param string  $id      Param ID
         * @param string  $profile Profile name
         * @param boolean $strict  Flag; whether to check the general profile or no
         *
         * @return boolean
         */
        function valuesExists($id, $profile = '', $strict = true) {
            if(!$profile) $profile = $this->currentProfile;
            $param = $this->getParam($id, $profile, $strict);
            return $param ? isset($param['values']) : false;
        }

        /**
         * Method to set values
         *
         * @param string $id      Param ID
         * @param array  $values  Param values
         * @param string $profile Profile name
         *
         * @return void
         */
        function setValues($id, $values, $profile = '') {
            if(!$profile) $profile = $this->currentProfile;
            if(isset($this->params[$profile][$id])) {
                $this->params[$profile][$id]['values'] = $values;
            } else if(isset($this->params[$this->generalProfile][$id])) {
                $this->params[$profile][$id] = $this->params[$this->generalProfile][$id];
                $this->params[$profile][$id]['values'] = $values;
            } //else param not exists
        }

        /**
         * Method to check param's value
         *
         * @param string  $id      Param ID
         * @param mixed   $value   Param values
         * @param string  $profile Profile name
         * @param boolean $strict  Flag; whether to check the general profile or no
         *
         * @return boolean
         */
        function checkValue($id, $value, $profile = '', $strict = false) {
            if(!$profile) $profile = $this->currentProfile;
            if(!is_array($value)) $value = array($value);
            return in_array(strtolower($this->getValue($id, $profile, $strict)), array_map('strtolower', $value));
        }

        /**
         * Method to check group
         *
         * @param string $id    Param ID
         * @param string $group Group name
         *
         * @return boolean
         */
        function checkGroup($id, $group/*, $profile = ''*/) {
            //if(!$profile) $profile = $this->currentProfile;
            if(!isset($this->params[$this->generalProfile][$id]['group']) || empty($this->params[$this->generalProfile][$id]['group'])) return false;
            if(!is_array($group)) $group = array($group);
            return in_array(strtolower($this->params[$this->generalProfile][$id]['group']), array_map('strtolower', $group));
        }

        /**
         * Method to get param's label
         *
         * @param string  $id      Param ID
         * @param string  $profile Profile name
         * @param boolean $strict  Flag; whether to check the general profile or no
         *
         * @return string|null
         */
        function getLabel($id, $profile = '', $strict = false) {
            if(!$profile) $profile = $this->currentProfile;
            $param = $this->getParam($id, $profile, $strict);
            return $param ? $param['label'] : null;
        }

        /**
         * Method to get param's description
         *
         * @param string  $id      Param ID
         * @param string  $profile Profile name
         * @param boolean $strict  Flag; whether to check the general profile or no
         *
         * @return string|null
         */
        function getDescription($id, $profile = '', $strict = false) {
            if(!$profile) $profile = $this->currentProfile;
            $param = $this->getParam($id, $profile, $strict);
            if($param) {
                return isset($param['description']) ? $param['description'] : '';
            } else return null;
        }

        /**
         * Method to get param's type
         *
         * @param string  $id      Param ID
         * @param string  $profile Profile name
         * @param boolean $strict  Flag; whether to check the general profile or no
         *
         * @return string|null
         */
        function getType($id, $profile = '', $strict = false) {
            if(!$profile) $profile = $this->currentProfile;
            $param = $this->getParam($id, $profile, $strict);
            return $param ? $param['type'] : null;
        }

        /**
         * Method to get param's subtype
         *
         * @param string  $id      Param ID
         * @param string  $profile Profile name
         * @param boolean $strict  Flag; whether to check the general profile or no
         *
         * @return string|null
         */
        function getSubType($id, $profile = '', $strict = false) {
            if(!$profile) $profile = $this->currentProfile;
            $param = $this->getParam($id, $profile, $strict);
            return $param ? (isset($param['subType']) ? $param['subType'] : null) : null;
        }

        /**
         * Method to set param's subtype
         *
         * @param string $id      Param ID
         * @param string $subType Param subtype
         * @param string $profile Profile name
         *
         * @return void
         */
        function setSubType($id, $subType, $profile = '') {
            if(!$profile) $profile = $this->currentProfile;
            if(isset($this->params[$profile][$id])) {
                $this->params[$profile][$id]['subType'] = $subType;
            } else if(isset($this->params[$this->generalProfile][$id])) {
                $this->params[$profile][$id] = $this->params[$this->generalProfile][$id];
                $this->params[$profile][$id]['subType'] = $subType;
            } //else param not exists
        }

        /**
         * Method to load params from INI file
         *
         * @param string $file    Path to INI file
         * @param string $profile Profile name
         *
         * @return boolean
         */
        function loadINI($file, $profile = '') {
            if(!$profile) $profile = $this->currentProfile;
            if(!file_exists($file)) return false;
            $ini = file($file);
            foreach($ini as $num=> $line) {
                $line = trim($line);
                if(empty($line) || in_array(substr($line, 0, 1), array(';','#'))) continue;
                $cur = explode('=', $line, 2);
                if(count($cur) != 2) {
                    error_log("WARNING: You have errors in you INI file ({$file}) on line " . ($num+1) . "!");
                    continue;
                }
                $this->setValue(trim($cur[0]), trim($cur[1]), $profile);
            }
            return true;
        }

        /**
         * Method to update INI file
         *
         * @param string $file    Path to INI file
         * @param array  $params  Params
         * @param string $profile Profile name
         *
         * @return boolean
         */
        function updateINI($file, $params = null, $profile = '') {
            if(!$profile) $profile = $this->currentProfile;
            if(!file_exists($file)) return false;
            $iniLines = file($file);
            $iniParams = array();
            foreach($iniLines as $num => $line) {
                $line = trim($line);
                if(empty($line) || in_array(substr($line, 0, 1), array(';','#'))) continue;
                list($id, $value) = explode('=', $line, 2);
                $id = trim($id);
                $iniParams[$id] = $num;
            }
            if($params === null) $params = array_keys($this->params[$profile]);

            foreach($params as $id) {
                if(isset($iniParams[$id])) {
                    $iniLines[$iniParams[$id]] = $id . ' = ' . $this->getValue($id, $profile) . "\n";
                } else {
                    $line = "\n";
                    if(isset($this->params[$profile][$id]['label'])) {
                        $line .= '# ' . $this->params[$profile][$id]['label'] . "\n";
                    }
                    if(isset($this->params[$profile][$id]['description'])) {
                        $line .= '# ' . $this->params[$profile][$id]['description'] . "\n";
                    }
                    if(isset($this->params[$profile][$id]['values'])) {
                        $line .= '# allowed values: ';
                        for($i = 0, $l = count($this->params[$profile][$id]['values']); $i < $l; $i++) {
                            $line .= $this->params[$profile][$id]['values'][$i];
                            if($i < $l-1) $line .= ', ';
                        }
                        $line .= "\n";
                    }
                    $iniLines[] = $line . $id . ' = ' . $this->getValue($id, $profile) . "\n";
                }
            }
            file_put_contents($file, implode("", $iniLines));

            return true;
        }

        /**
         * Method to set mapping
         *
         * @param array  $mapping  Mapping
         *
         * @return void
         */
        function setMapping($mapping = array()) {
            $this->mapping = $mapping;
        }

        /**
         * Method to get mapping
         *
         * @return array
         */
        function getMapping() {
            return $this->mapping;
        }

        /**
         * Method to add mapping
         *
         * @param string $key      Param ID
         * @param array  $mapping  Mapping
         *
         * @return void
         */
        function addMapping($key, $mapping = array()) {
            $this->mapping[$key] = $mapping;
        }

        /**
         * Method to remove mapping
         *
         * @param string $key      Param ID
         *
         * @return void
         */
        function removeMapping($key) {
            if(isset($this->mapping[$key])) unset($this->mapping[$key]);
        }

        /**
         * Method to serialize params
         *
         * @param boolean $script    Flag; serialize to script or rel
         * @param string  $delimiter Delimiter
         * @param string  $profile   Profile name
         *
         * @return string
         */
        function serialize($script = false, $delimiter = '', $profile = '') {
            if(!$profile) $profile = $this->currentProfile;
            $serializeAll = $profile == $this->generalProfile;
            $str = array();
            foreach($this->getParams($this->generalProfile) as $param) {
                if(!isset($param['scope']) || ($param['scope'] != $this->scope) || !$this->paramExists($param['id'], $profile) || !$serializeAll && $this->checkValue($param['id'], $this->getValue($param['id'], $this->generalProfile), $profile)) {
                    continue;
                }
                $value = $this->getValue($param['id'], $profile);
                if(isset($this->mapping[$param['id']])) {
                    if(is_array($this->mapping[$param['id']])) {
                        if(array_key_exists($value, $this->mapping[$param['id']])) {
                            $value = $this->mapping[$param['id']][$value];
                        }
                    } else/*lambda-style function*/ {
                        $value = $this->mapping[$param['id']]($this);
                    }
                    //add possibility to skip some parameters with a specific value
                    if($value === null) continue;
                }
                if($script) {
                    switch($param['type']) {
                        case 'num':
                            //TODO: spike for the parameters that can be numeric or string
                            //if(strpos($value, "%") !== false) {
                            //    $value = '\'' . $value . '\'';
                            //    break;
                            //}
                        case 'float':
                            if($value != 'auto') break;
                        case 'text':
                            //NOTE: escape single quotes
                            $value = '\''.str_replace('\'', '\\\'', $value).'\'';//preg_replace('/(?<!\\\\)\'/s', '\\\'', $value)
                            break;
                        case 'array':
                            // NOTE: for magiczoom(plus) 'right-click' param: 'false' and 'true' values are not boolean
                            if(in_array($value, array('false', 'true')) && $param['id'] != 'right-click') break;
                        default:
                            $value = '\'' . $value . '\'';
                    }
                    $str[]= '\'' . $param['id'] . '\':' . $value;
                } else /*rel*/ {
                    $str[] = $param['id'] . ':' . $value;
                }
            }
            if(empty($str)) {
                $str = '';
            } else {
                if(!$delimiter) {
                    $delimiter = $script ? ',' : ';';
                }
                $str = implode($delimiter, $str);
                if(!$script) $str .= $delimiter;
            }

            return $str;
        }

        /**
         * Method to unserialize params
         *
         * @param string  $str     Params string
         * @param string  $profile Profile name
         *
         * @return boolean
         */
        function unserialize($str, $profile = '') {
            if(!$profile) $profile = $this->currentProfile;
            //script version
            //preg_match_all("/'([a-z_\-]+)':'?([^;']*)'?/ui", $str, $matches);
            //rel version
            preg_match_all("/([a-z_\-]+):([^;']*)/ui", $str, $matches);
            if(count($matches[1]) > 0) {
                $options = array_combine($matches[1], $matches[2]);
                foreach($options as $name => $value) {
                    $this->setValue($name, $value, $profile);
                }
                return true;
            }
            return false;
        }

    }

}

?>
