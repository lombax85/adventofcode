<?php
/**
 * 10.08 - 10.16 - 11.00 (in call...)
 *
 *
 *
 * Super Regex by Sharpedge
 *
 * import re
input_file = open('puzzle4.txt', 'r')

filetext = input_file.read()
input_file.close()

sol_1 = re.compile("(?:(?:byr|hgt|eyr|ecl|pid|iyr|hcl):[^\s]+\s?(?:cid:[^\s]+\s?)?){7,}")
sol_2 = re.compile("(?:(?:byr:(19[2-8][0-9]|199[0-9]|200[0-2])|hgt:((1[5-8][0-9]|19[0-3])cm|(59|6[0-9]|7[0-6])in)|eyr:(202[0-9]|2030)|ecl:(amb|blu|brn|gry|grn|hzl|oth)|pid:[0-9]{9}|iyr:(201[0-9]|2020)|hcl:(#[a-f0-9]{6}))\s?(?:cid:[^\s]+\s?)?){7,}")

print(len(sol_1.findall(filetext)))
print(len(sol_2.findall(filetext)))
 *
 */


$data = file_get_contents('./4_dic.txt');

$passports = explode("\n\n", $data);


$needed_fields = [
    'byr:',
    'iyr:',
    'eyr:',
    'hgt:',
    'hcl:',
    'ecl:',
    'pid:',
];

$valid = 0;
foreach ($passports as $passport) {
    $vnum = 0;
    foreach ($needed_fields as $field) {
        if (strpos($passport, $field) !== false)
            $vnum++;
    }
    if ($vnum == 7)
        $valid++;
}


echo $valid;

// ex 2

$valid = 0;
foreach ($passports as $passport) {
    $vnum = 0;
    foreach ($needed_fields as $field) {
        if (strpos($passport, $field) !== false)
            $vnum++;
    }
    if ($vnum == 7) {
        // we have a valid passport, now check the fields

        $nValid = 0;
        $fieldarr = explode(" ", implode(" ", explode("\n", $passport)));
        foreach ($fieldarr as $singleField) {
            $components = explode(":", $singleField);
            $key = $components[0];
            $value = $components[1];

            if ($key == "cid")
                continue;

            //echo "Evaluating $key:$value result is ".validate($key, $value)."\n";
            if (validate($key, $value) == true)
                $nValid++;

        }

        if ($nValid == 7)
            $valid++;

        //echo "\n Number of valid fields: $nValid \n\n\n\n";

    }
}

echo "\n";
echo $valid;

function validate($key, $value) {
    switch ($key) {
        case "byr":
            $valid = true;
            if (strlen($value) != 4)
                $valid = false;

            if (intval($value) < 1920 || intval($value) > 2002)
                $valid = false;

            return $valid;
            break;

        case "iyr":
            $valid = true;
            if (strlen($value) != 4)
                $valid = false;

            if (intval($value) < 2010 || intval($value) > 2020)
                $valid = false;

            return $valid;
            break;
        case "eyr":
            $valid = true;
            if (strlen($value) != 4)
                $valid = false;

            if (intval($value) < 2020 || intval($value) > 2030)
                $valid = false;

            return $valid;
            break;
        case "hgt":

            if (substr($value, -2) == "cm") {
                if (intval($value) >= 150 && intval($value) <= 193)
                    return true;
                else
                    return false;
            }

            if (substr($value, -2) == "in") {
                if (intval($value) >= 59 && intval($value) <= 76)
                    return true;
                else
                    return false;
            }

            break;
        case "hcl":
            return preg_match("/^#[0-9a-fA-F][0-9a-fA-F][0-9a-fA-F][0-9a-fA-F][0-9a-fA-F][0-9a-fA-F]$/", $value);
            break;
        case "ecl":
            $valid = false;

            if (in_array($value, ['amb', 'blu', 'brn', 'gry', 'grn', 'hzl', 'oth']))
                $valid = true;

            return $valid;

            break;
        case "pid":
            return preg_match("/^[0-9]{9}$/", $value);
            break;
        default:
            return true;
    }


}