<?php

namespace Pheonix;

class Parser
{
    private $text = null;
    private $data = [];

    // Settings
    private $scope_glue = '.';
    private $tag_regex = '';
    private $variable_regex = '';
    private $variable_loop_regex = '';

    public function parse(string $text, array $data = [], $callback = null)
    {
        $this->setup_regex();
        $this->remove_comments();

        $this->data = $data;
        $this->text = $this->parse_variables($text);

        return $this->text;
    }

    public function setup_regex()
    {
        $glue = preg_quote($this->scope_glue, '/');

        $this->tag_regex = '/\{\{(.*?)\}\}/';
        $this->variable_regex = sprintf('/\{\{\s*([a-zA-Z0-9_%s]+)\s*\}\}/m', $glue);
        $this->variable_loop_regex = sprintf('/\{\{\s*([a-zA-Z0-9_%s]+)\s*\}\}(.*?)\{\{\s*\/\1\s*\}\}/ms', $glue);
    }

    public function remove_comments()
    {
        $this->text = preg_replace('/\{\{#.*?#\}\}/s', '', $this->text);
    }

    public function parse_variables(string $text, array $data = [])
    {
        if (empty($data)) {
            $data = $this->data;
        }

        // if (preg_match_all($this->variable_loop_regex, $text, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE)) {
        //     foreach ($matches as $key => $match) {
        //         if ($loop_data = $this->get_variable($match[1][0], $data)) {
        //             $looped_text = '';
        //             foreach ($loop_data as $item_data) {
        //                 $looped_text .= $this->parse_variables($match[2][0], $item_data);
        //             }
        //             $text = preg_replace(sprintf('/%s/m', preg_quote($match[0][0], '/')), $looped_text, $text, 1);
        //         }
        //     }
        // }

        if (preg_match_all($this->variable_regex, $text, $matches)) {
            foreach ($matches[1] as $key => $variable) {
                // $text = str_replace($matches[0][$key], $this->get_variable($variable, $data), $text);
                extract($data);
                $text = str_replace($matches[0][$key], $$variable, $text);
                // echo $text;
            }
        }

        return $text;
    }

    public function get_variable($variable, $data)
    {
        foreach (explode($this->scope_glue, $variable) as $part) {
            if (!isset($data[$part])) {
                return null;
            }
            $data = &$data[$part];
        }

        if (is_array($data)) {
            return $data;
        }

        return $data;
    }
}
