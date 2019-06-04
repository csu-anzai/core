interface SearchResult {
    description : String,
    icon : String,
    link : String,
    module : String,
    score : String,
    systemid : String
}
interface FilterModule{
    module : string,
    id : number
}
export { SearchResult, FilterModule }
