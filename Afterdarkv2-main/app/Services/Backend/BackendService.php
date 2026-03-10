<?php

namespace App\Services\Backend;

use App\Constants\RoleConstants;
use App\Models\Category;
use App\Models\City;
use App\Models\Country;
use App\Models\CountryLanguage;
use App\Models\Genre;
use App\Models\PodcastCategory;
use App\Models\RadioCategory;
use App\Models\Region;
use App\Models\Tag;
use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use Spatie\Permission\Models\Role;

class BackendService
{
    public static function fileSizeConverter(int $bytes): string
    {
        if ($bytes == 0) {
            return '';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    public static function insertMissingData(array $originalData, array $types, Carbon $startDate, Carbon $endDate): array
    {
        $dates = [];
        $data = [];

        foreach ($originalData as $item) {
            $dates[] = $item['date'];
            $data[] = $item;
        }

        sort($dates);

        $period = new DatePeriod(
            $startDate,
            new DateInterval('P1D'),
            $endDate
        );

        foreach ($period as $d) {
            $key = $d->format('Y-m-d');

            if (!in_array($key, $dates)) {
                $array = [];

                foreach ($types as $type) {
                    $array[$type] = 0;
                }

                $array['date'] = $key;
                $data[] = $array;
            }
        }

        usort($data, self::sortArrayByDate(...));

        return $data;
    }

    public static function makeTagSelector($name, $tags = ''): string
    {
        $allTags = Tag::all('tag')->unique('tag')->pluck('tag')->toArray();

        $output = "<select name=\"$name\" class=\"form-control select2-tags\"  multiple=\"multiple\">";

        if ($tags && !is_array($tags)) {
            $tags = explode(',', $tags);
        }

        foreach ($allTags as $defaultTag) {
            $output .= "<option value=\"$defaultTag\"";

            if ($tags && in_array($defaultTag, $tags)) {
                $output .= ' selected ';
            }

            $output .= ">$defaultTag</option>";
        }

        $output .= '</select>';

        return $output;
    }

    public static function makeCheckbox($name, $selected = false): string
    {
        $selected ? $selected = 'checked' : $selected = '';

        return "<input type=\"hidden\" name=\"$name\" value=\"0\" /><input type=\"checkbox\" name=\"$name\" value=\"1\" $selected>";
    }

    public static function makeDropdown($options, $name, $selected = null, $disableFirstOption = true): string
    {
        $output = "<select name=\"$name\" class=\"form-select select2-active select2\">";

        if (!$disableFirstOption) {
            $output .= '<option disabled selected value></option>';
        }

        foreach ($options as $value => $description) {
            $output .= "<option value=\"$value\"";

            if ($selected == $value) {
                $output .= ' selected ';
            }

            $output .= ">$description</option>";
        }

        $output .= '</select>';

        return $output;
    }

    public static function makeRolesDropdown($name, $selected = null, $required = ''): string
    {
        $output = "<select name=\"$name\" class=\"form-control select2-active\" $required><option></option>";

        foreach (Role::all() as $role) {
            if ($role->id != RoleConstants::GUEST) {
                $output .= "<option value=\"$role->id\"";

                if ($selected == $role->id) {
                    $output .= ' selected ';
                }

                $output .= ">{$role->name}</option>";
            }
        }

        $output .= '</select>';

        return $output;
    }

    public static function radioCategorySelection($categoryId = 0, $parentId = 0, $nocat = true, $sublevelMarker = '', $returnString = ''): string
    {
        $rows = RadioCategory::all();

        $catInfo = [];

        foreach ($rows as $row) {
            $catInfo[$row->id] = [];
            $catInfo[$row->id] = json_decode(json_encode($row), true);
        }

        if (count($catInfo)) {
            foreach ($catInfo as $key) {
                $cat[$key['id']] = $key['name'];
                $catParentId[$key['id']] = $key['parent_id'];
            }
        }

        if ($parentId == 0) {
            $sublevelMarker = '';
            // if( $nocat ) $returnString .= '<option value="0"></option>';
        } else {
            $sublevelMarker .= '&nbsp;&nbsp;&nbsp;&nbsp;';
        }

        if (isset($catParentId)) {
            $rootCategory = @array_keys($catParentId, $parentId);

            if (is_array($rootCategory)) {
                foreach ($rootCategory as $id) {
                    $categoryName = $cat[$id];
                    $color = 'black';
                    $returnString .= '<option value="' . $id . '" ';

                    if (is_array($categoryId)) {
                        foreach ($categoryId as $element) {
                            if ($element == $id) {
                                $returnString .= 'SELECTED';
                            }
                        }
                    } elseif ($categoryId == $id) {
                        $returnString .= 'SELECTED';
                    }
                    $returnString .= '>' . $sublevelMarker . $categoryName . '</option>';
                    $returnString = self::radioCategorySelection($categoryId, $id, $nocat, $sublevelMarker, $returnString);
                }
            }
        }

        return $returnString;
    }

    public static function categorySelection($categoryId = 0, $parentId = 0, $nocat = true, $sublevelMarker = '', $returnString = ''): string
    {
        $rows = Category::all();
        $catInfo = [];

        foreach ($rows as $row) {
            $catInfo[$row->id] = [];
            $catInfo[$row->id] = json_decode(json_encode($row), true);
        }

        if (count($catInfo)) {
            foreach ($catInfo as $key) {
                $cat[$key['id']] = $key['name'];
                $catParentId[$key['id']] = $key['parent_id'];
            }
        }

        if ($parentId == 0) {
            $sublevelMarker = '';
            // if( $nocat ) $returnString .= '<option value="0"></option>';
        } else {
            $sublevelMarker .= '&nbsp;&nbsp;&nbsp;&nbsp;';
        }

        if (isset($catParentId)) {
            $rootCategory = @array_keys($catParentId, $parentId);

            if (is_array($rootCategory)) {
                foreach ($rootCategory as $id) {
                    $categoryName = $cat[$id];
                    $color = 'black';
                    $returnString .= '<option value="' . $id . '" ';

                    if (is_array($categoryId)) {
                        foreach ($categoryId as $element) {
                            if ($element == $id) {
                                $returnString .= 'SELECTED';
                            }
                        }
                    } elseif ($categoryId == $id) {
                        $returnString .= 'SELECTED';
                    }

                    $returnString .= '>' . $sublevelMarker . $categoryName . '</option>';

                    $returnString = self::categorySelection($categoryId, $id, $nocat, $sublevelMarker, $returnString);
                }
            }
        }

        return $returnString;
    }

    public static function groupPermission($access): ?array
    {
        if (!$access) {
            return null;
        }

        $data = [];
        $groups = explode('||', $access);

        foreach ($groups as $group) {
            [$groupId, $groupValue] = explode(':', $group);
            $data[$groupId] = $groupValue;
        }

        return $data;
    }

    public static function genreSelection($categoryId = 0, $parentId = 0, $nocat = true, $sublevelMarker = '', $returnString = '', $limit = false): string
    {
        $catInfo = [];

        foreach (Genre::query()->discover()->get() as $row) {
            $catInfo[$row->id] = json_decode(json_encode($row), true);
        }

        if (count($catInfo)) {
            foreach ($catInfo as $key) {
                $cat[$key['id']] = $key['name'];
                $catParentId[$key['id']] = $key['parent_id'];
            }
        }

        if ($parentId == 0) {
            // Disable for select2
            // if( $nocat ) $returnString .= '<option value="0"></option>';
        } else {
            $sublevelMarker .= '&nbsp;&nbsp;&nbsp;&nbsp;';
        }

        if (isset($catParentId)) {
            $rootCategory = @array_keys($catParentId, $parentId);

            if (is_array($rootCategory)) {
                foreach ($rootCategory as $id) {
                    $categoryName = $cat[$id];
                    $color = 'black';
                    $returnString .= "<option style=\"color: {$color}\" value=\"" . $id . '" ';

                    if (is_array($categoryId)) {
                        foreach ($categoryId as $element) {
                            if ($element == $id) {
                                $returnString .= 'SELECTED';
                            }
                        }
                    } elseif ($categoryId == $id) {
                        $returnString .= 'SELECTED';
                    }
                    $returnString .= '>' . $sublevelMarker . $categoryName . '</option>';
                }
            }
        }

        return $returnString;
    }

    public static function makeChannelDropDown($options, $name, $selected): string
    {
        $output = "<select name=\"$name\" class=\"form-control slide-show-type\"><option disabled selected value></option>";

        foreach ($options as $value => $description) {
            $output .= "<option value=\"$value\"";
            if ($selected == $value) {
                $output .= ' selected ';
            }
            $output .= ">$description</option>";
        }
        $output .= '</select>';

        return $output;
    }

    public static function podcastCategorySelection($categoryId = 0, $parentId = 0, $nocat = true, $sublevelMarker = '', $returnString = ''): string
    {
        $rows = PodcastCategory::all();

        $catInfo = [];

        foreach ($rows as $row) {
            $catInfo[$row->id] = [];
            $catInfo[$row->id] = json_decode(json_encode($row), true);
        }

        if (count($catInfo)) {
            foreach ($catInfo as $key) {
                $cat[$key['id']] = $key['name'];
                $catParentId[$key['id']] = $key['parent_id'];
            }
        }

        if ($parentId == 0) {
            $sublevelMarker = '';
            // if( $nocat ) $returnString .= '<option value="0"></option>';
        } else {
            $sublevelMarker .= '&nbsp;&nbsp;&nbsp;&nbsp;';
        }

        if (isset($catParentId)) {
            $rootCategory = @array_keys($catParentId, $parentId);

            if (is_array($rootCategory)) {
                foreach ($rootCategory as $id) {
                    $categoryName = $cat[$id];
                    $color = 'black';
                    $returnString .= '<option value="' . $id . '" ';

                    if (is_array($categoryId)) {
                        foreach ($categoryId as $element) {
                            if ($element == $id) {
                                $returnString .= 'SELECTED';
                            }
                        }
                    } elseif ($categoryId == $id) {
                        $returnString .= 'SELECTED';
                    }
                    $returnString .= '>' . $sublevelMarker . $categoryName . '</option>';
                    $returnString = self::podcastCategorySelection($categoryId, $id, $nocat, $sublevelMarker, $returnString);
                }
            }
        }

        return $returnString;
    }

    public static function makeCountryDropdown($name, $class, $selected = null): string
    {
        $output = "<select name=\"$name\" class=\"$class\" style=\"display: none\"><option disabled selected value></option>";

        foreach (Country::all() as $country) {
            $output .= "<option value=\"$country->id\"";

            if ($selected == $country->id) {
                $output .= ' selected ';
            }

            $output .= ">$country->name</option>";
        }

        $output .= '</select>';

        return $output;
    }

    public static function makeCityDropdown($countryId, $name, $class, $selected = null): string
    {
        $output = "<select name=\"$name\" class=\"$class\" style=\"display: none\"><option disabled selected value></option>";

        foreach (City::query()->where('country_id', $countryId)->get() as $city) {
            $output .= "<option value=\"$city->id\"";

            if ($selected == $city->id) {
                $output .= ' selected ';
            }

            $output .= ">$city->name</option>";
        }

        $output .= '</select>';

        return $output;
    }

    public static function makeCountryLanguageDropdown($countryId, $name, $class, $selected = null): string
    {
        $output = "<select name=\"$name\" class=\"$class\" style=\"display: none\"><option disabled selected value></option>";

        foreach (CountryLanguage::query()->where('country_id', $countryId)->get() as $language) {
            $output .= "<option value=\"$language->id\"";

            if ($selected == $language->id) {
                $output .= ' selected ';
            }

            $output .= ">$language->name</option>";
        }

        $output .= '</select>';

        return $output;
    }

    public static function makeRegionDropdown($name, $class, $selected = null): string
    {
        $output = "<select name=\"$name\" class=\"$class\"><option disabled selected value></option>";

        foreach (Region::all() as $region) {
            $output .= "<option value=\"$region->id\"";

            if ($selected == $region->id) {
                $output .= ' selected ';
            }

            $output .= ">$region->name</option>";
        }

        $output .= '</select>';

        return $output;
    }

    private static function sortArrayByDate($a, $b): int
    {
        return strtotime($a['date']) - strtotime($b['date']);
    }
}
