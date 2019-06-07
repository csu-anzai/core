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
interface User {
    icon : string,
    label : string,
    systemid : string,
    title : string,
    value : string
}
export { SearchResult, FilterModule, User }
