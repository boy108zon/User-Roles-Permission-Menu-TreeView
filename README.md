# User-Roles-Permission-Menu-TreeView
User Roles , Permission , Menu,Tree

I am using codeigniter 3 so below function will go in your controller so that you can fetch data which is needed to build module & permission

I have added the screenshot of image so that it may help anyone.
this is a helper fuction to find out permission id exist in all module permission multidimention array.
.................................................
function inMultiArray($needle, $heystack) {
    if (!empty($heystack)) {
        if (array_key_exists($needle, $heystack) or in_array($needle, $heystack)) {
            return true;
        } else {
            $return = false;
            foreach (array_values($heystack) as $value) {
                if (is_array($value) and ! $return) {
                    $return = inMultiArray($needle, $value);
                }
            }
            return $return;
        }
    }
}
