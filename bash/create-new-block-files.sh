# This script will create the files and folders required by Block Layouts for a new Block / Layout to appear in the CMS

#Directories
Blocks="src/Models/Blocks"
Templates="templates/Toast/Blocks/Default"
Icons="client/images/layout-icons/default"

# function to make string UpperCamelCase
function to_camel_case {
    str=$1
    str="$(echo $str | awk 'BEGIN{OFS=""};{for(j=1;j<=NF;j++){ if(j==1){$j=tolower($j)} else {$j=toupper(substr($j,1,1)) tolower(substr($j,2)) }}}1')"
    str="$(tr '[:lower:]' '[:upper:]' <<< ${str:0:1})${str:1}"
    echo $str
}

# Function to convert a string from UpperCamelCase to Sentence Case
function from_camel_to_sentence_case {
    str=$1
    str="$(tr '[:upper:]' '[:lower:]' <<< ${str:0:1})${str:1}"
    str="$(echo $str | sed 's/\([a-z0-9]\)\([A-Z]\)/\1 \2/g')"
    # Make the first letter of each word uppercase
    str="$(echo $str | awk '{for(i=1;i<=NF;i++){ $i=toupper(substr($i,1,1)) tolower(substr($i,2)) } print}')"
    echo $str
}

# Convert the template to UpperCamelCase
template=$(to_camel_case "default")

# Save the template name to a variable for later use
TemplateDirectory="$Templates"
# Convert the template name to lowercase for use in the icon path
IconDirectory="$Icons"

# Function to enter a new block name
function enter_block_name {
    read -p "Enter the name of the new block: " block

    # Split the block name by spaces into an array
    block=($block)
    # Loop through the array and capitalise each word
    for ((i=0; i<${#block[@]}; i++)); do
        # Capitalise the first letter of the word
        block[$i]="$(tr '[:lower:]' '[:upper:]' <<< ${block[$i]:0:1})${block[$i]:1}"
    done

    # Convert the array back into a string
    block="${block[@]}"
    
    # Remove spaces from the block name
    block=$(echo $block | sed 's/ //g')
    # block=$(to_camel_case "$block")
    if [[ ! $block =~ Block$ ]]; then
        block="$block""Block"
    fi
    read -p "Using block name: $block. Is this correct? (y) " correct
    if [ "$correct" = "n" ]; then
        enter_block_name
    else
        return
    fi
}

# Ask the user to enter a new block name
enter_block_name

# If the template folder doesn't exist, create it
if [ ! -d "$TemplateDirectory" ]; then
    mkdir "$TemplateDirectory"
fi

# If the icon folder doesn't exist, create it
if [ ! -d "$IconDirectory" ]; then
    mkdir "$IconDirectory"
fi

# Save the template name to a variable for later use
TemplateFile="$TemplateDirectory/$block.ss"
# Save the icon name to a variable for later use, converting the block name to lowercase
IconFile="$IconDirectory/$(echo $block | tr '[:upper:]' '[:lower:]').svg"
# Save the block name to a variable for later use
BlockFile="$Blocks/$block.php"

# If the TemplateFile doesnt exist, create it
if [ ! -f "$TemplateFile" ]; then
    touch "$TemplateFile"

    # Remove 'Block' from the end of the block name
    LowerBlockNameWithoutBlock=$(echo $block | sed 's/Block$//');
    # Insert a hyphen before each capital letter
    LowerBlockNameWithoutBlock=$(echo $LowerBlockNameWithoutBlock | sed 's/\([A-Z]\)/-\1/g' | sed 's/^-//g');
    # lower case the block name
    LowerBlockNameWithoutBlock=$(echo $LowerBlockNameWithoutBlock | tr '[:upper:]' '[:lower:]');


    # BlockName=$(from_camel_to_sentence_case "$block");

    # LowerBlockName=$(echo $block | tr '[:upper:]' '[:lower:]');
    # LowerBlockNameWithoutBlock=$(echo $LowerBlockName | sed 's/block$//');

    # # 

    TemplateName=$(from_camel_to_sentence_case "$template");
    LowerTemplateName=$(echo $template | tr '[:upper:]' '[:lower:]');

    # The default html we want is like this <section id="{$HTMLID}" class="default-showcase [ js-showcase ] background-colour--{$getColourForTemplate($PrimaryColour)} {$IncludeClasses} {$ExtraClasses}">

    # Write some default content to the template file
    echo "<section id=\"{\$HTMLID}\" class=\"$LowerTemplateName-$LowerBlockNameWithoutBlock [ js-$LowerTemplateName-$LowerBlockNameWithoutBlock ] background-colour--{\$getColour(\$PrimaryColour, 'class, brightness')} {\$IncludeClasses} {\$ExtraClasses}\">

</section>" > "$TemplateFile"

fi

# If the IconFile doesnt exist, create it
if [ ! -f "$IconFile" ]; then
    touch "$IconFile"
fi

# If the BlockFile doesnt exist and the VendorBlockFile doesn't exist, create the BlockFile
if [ ! -f "$BlockFile" ]; then
    touch "$BlockFile"

    BlockName=$(from_camel_to_sentence_case "$block");

    # Write some default content to the block file
    echo "<?php

namespace Toast\Blocks;

class $block extends Block
{
    private static \$singular_name = '$BlockName';

    private static \$plural_name = '${BlockName}s';

    private static \$description = '$BlockName';

    private static \$table_name = '$block';

    private static $db = [
    ];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        return $fields;
    }
}

" > "$BlockFile"

fi

# function to open the files in VS Code
function open_files_in_vs_code {
    # Ask if the user wants to open the files in VS Code, if yes, open the files, if no, do nothing
    read -p "Open files in VS Code? (y/n) " open

    if [ "$open" = "y" ]; then
        open="true"
    else if [ "$open" = "n" ]; then
        return
    else
        open_files_in_vs_code
    fi
    fi

    if [ "$open" = "true" ]; then
        # Open the files in VS Code
        code "$TemplateFile" "$IconFile" "$StylesFile" "$ScriptsFile" "$YMLConfigFile"

        # If the BlockFile exists, open it in VS Code
        if [ -f "$BlockFile" ]; then
            code "$BlockFile"
        fi
    fi
}

# Open the files in VS Code
open_files_in_vs_code

exit