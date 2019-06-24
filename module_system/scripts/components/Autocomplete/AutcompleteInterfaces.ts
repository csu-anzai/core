interface AutocompleteInterface {
     parsedAutoCompleteData : Array<AutocompleteItem>
}

interface AutocompleteItem {
    title : string,
    label : string,
    value : string | Number
}

export { AutocompleteItem, AutocompleteInterface }
