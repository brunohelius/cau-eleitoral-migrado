namespace CAU.Eleitoral.Application.DTOs;

/// <summary>
/// Generic paged result for API responses
/// </summary>
/// <typeparam name="T">Type of items</typeparam>
public record PagedResult<T>
{
    public IEnumerable<T> Items { get; init; } = new List<T>();
    public int TotalCount { get; init; }
    public int Page { get; init; }
    public int PageSize { get; init; }
    public int TotalPages => PageSize > 0 ? (int)Math.Ceiling(TotalCount / (double)PageSize) : 0;
    public bool HasNextPage => Page < TotalPages;
    public bool HasPreviousPage => Page > 1;

    public static PagedResult<T> Empty => new()
    {
        Items = new List<T>(),
        TotalCount = 0,
        Page = 1,
        PageSize = 20
    };

    public static PagedResult<T> FromItems(IEnumerable<T> items, int totalCount, int page, int pageSize)
    {
        return new PagedResult<T>
        {
            Items = items,
            TotalCount = totalCount,
            Page = page,
            PageSize = pageSize
        };
    }
}
