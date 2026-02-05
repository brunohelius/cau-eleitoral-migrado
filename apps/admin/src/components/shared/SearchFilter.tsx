import * as React from "react"
import { Search, X, Filter, SlidersHorizontal } from "lucide-react"
import { Input } from "@/components/ui/input"
import { Button } from "@/components/ui/button"
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select"
import { cn } from "@/lib/utils"

export interface FilterOption {
  value: string
  label: string
}

export interface FilterConfig {
  key: string
  label: string
  placeholder?: string
  options: FilterOption[]
}

export interface SearchFilterProps {
  searchValue: string
  onSearchChange: (value: string) => void
  searchPlaceholder?: string
  filters?: FilterConfig[]
  filterValues?: Record<string, string>
  onFilterChange?: (key: string, value: string) => void
  onClearFilters?: () => void
  showClearButton?: boolean
  debounceMs?: number
  className?: string
  children?: React.ReactNode
}

export function SearchFilter({
  searchValue,
  onSearchChange,
  searchPlaceholder = "Buscar...",
  filters = [],
  filterValues = {},
  onFilterChange,
  onClearFilters,
  showClearButton = true,
  debounceMs = 300,
  className,
  children,
}: SearchFilterProps) {
  const [localSearch, setLocalSearch] = React.useState(searchValue)
  const debounceRef = React.useRef<ReturnType<typeof setTimeout> | null>(null)

  // Sync external value
  React.useEffect(() => {
    setLocalSearch(searchValue)
  }, [searchValue])

  // Debounced search
  const handleSearchChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const value = e.target.value
    setLocalSearch(value)

    if (debounceRef.current) {
      clearTimeout(debounceRef.current)
    }

    debounceRef.current = setTimeout(() => {
      onSearchChange(value)
    }, debounceMs)
  }

  // Cleanup on unmount
  React.useEffect(() => {
    return () => {
      if (debounceRef.current) {
        clearTimeout(debounceRef.current)
      }
    }
  }, [])

  const handleClearSearch = () => {
    setLocalSearch("")
    onSearchChange("")
  }

  const hasActiveFilters = Object.values(filterValues).some((v) => v && v !== "all")
  const hasSearch = localSearch.length > 0

  return (
    <div className={cn("flex flex-col gap-4 sm:flex-row sm:items-center", className)}>
      {/* Search Input */}
      <div className="relative flex-1 max-w-sm">
        <Search className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
        <Input
          type="text"
          placeholder={searchPlaceholder}
          value={localSearch}
          onChange={handleSearchChange}
          className="pl-9 pr-9"
        />
        {hasSearch && (
          <button
            type="button"
            onClick={handleClearSearch}
            className="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground"
          >
            <X className="h-4 w-4" />
          </button>
        )}
      </div>

      {/* Filters */}
      {filters.length > 0 && (
        <div className="flex flex-wrap items-center gap-2">
          {filters.map((filter) => (
            <Select
              key={filter.key}
              value={filterValues[filter.key] || "all"}
              onValueChange={(value) => onFilterChange?.(filter.key, value)}
            >
              <SelectTrigger className="w-[150px]">
                <SelectValue placeholder={filter.placeholder || filter.label} />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="all">Todos</SelectItem>
                {filter.options.map((option) => (
                  <SelectItem key={option.value} value={option.value}>
                    {option.label}
                  </SelectItem>
                ))}
              </SelectContent>
            </Select>
          ))}
        </div>
      )}

      {/* Custom children (additional filters/actions) */}
      {children}

      {/* Clear filters button */}
      {showClearButton && (hasActiveFilters || hasSearch) && (
        <Button
          variant="ghost"
          size="sm"
          onClick={() => {
            handleClearSearch()
            onClearFilters?.()
          }}
          className="text-muted-foreground"
        >
          <X className="mr-1 h-4 w-4" />
          Limpar filtros
        </Button>
      )}
    </div>
  )
}

// Compact version for toolbars
export interface CompactSearchProps {
  value: string
  onChange: (value: string) => void
  placeholder?: string
  className?: string
}

export function CompactSearch({
  value,
  onChange,
  placeholder = "Buscar...",
  className,
}: CompactSearchProps) {
  return (
    <div className={cn("relative", className)}>
      <Search className="absolute left-2.5 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
      <Input
        type="text"
        placeholder={placeholder}
        value={value}
        onChange={(e) => onChange(e.target.value)}
        className="h-8 pl-8 pr-8 text-sm"
      />
      {value && (
        <button
          type="button"
          onClick={() => onChange("")}
          className="absolute right-2.5 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground"
        >
          <X className="h-3.5 w-3.5" />
        </button>
      )}
    </div>
  )
}

// Advanced filter panel (for more complex filtering needs)
export interface AdvancedFilterPanelProps {
  isOpen: boolean
  onToggle: () => void
  onApply: () => void
  onClear: () => void
  children: React.ReactNode
  className?: string
}

export function AdvancedFilterPanel({
  isOpen,
  onToggle,
  onApply,
  onClear,
  children,
  className,
}: AdvancedFilterPanelProps) {
  return (
    <div className={cn("space-y-4", className)}>
      <Button variant="outline" onClick={onToggle}>
        <SlidersHorizontal className="mr-2 h-4 w-4" />
        Filtros avancados
      </Button>

      {isOpen && (
        <div className="rounded-lg border bg-card p-4 shadow-sm">
          <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            {children}
          </div>
          <div className="mt-4 flex justify-end gap-2">
            <Button variant="ghost" onClick={onClear}>
              Limpar
            </Button>
            <Button onClick={onApply}>
              Aplicar filtros
            </Button>
          </div>
        </div>
      )}
    </div>
  )
}

export default SearchFilter
