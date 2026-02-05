import * as React from "react"
import { useNavigate } from "react-router-dom"
import { Loader2, Save, X, Check, ArrowLeft, Trash2, RotateCcw } from "lucide-react"
import { Button } from "@/components/ui/button"
import { cn } from "@/lib/utils"

export interface FormActionsProps {
  onSave?: () => void | Promise<void>
  onCancel?: () => void
  onDelete?: () => void
  onReset?: () => void
  saveLabel?: string
  cancelLabel?: string
  deleteLabel?: string
  resetLabel?: string
  isSaving?: boolean
  isDeleting?: boolean
  isDisabled?: boolean
  showCancel?: boolean
  showDelete?: boolean
  showReset?: boolean
  cancelHref?: string
  align?: "left" | "center" | "right" | "between"
  className?: string
  children?: React.ReactNode
}

export function FormActions({
  onSave,
  onCancel,
  onDelete,
  onReset,
  saveLabel = "Salvar",
  cancelLabel = "Cancelar",
  deleteLabel = "Excluir",
  resetLabel = "Resetar",
  isSaving = false,
  isDeleting = false,
  isDisabled = false,
  showCancel = true,
  showDelete = false,
  showReset = false,
  cancelHref,
  align = "right",
  className,
  children,
}: FormActionsProps) {
  const navigate = useNavigate()

  const handleCancel = () => {
    if (onCancel) {
      onCancel()
    } else if (cancelHref) {
      navigate(cancelHref)
    } else {
      navigate(-1)
    }
  }

  const alignmentClasses = {
    left: "justify-start",
    center: "justify-center",
    right: "justify-end",
    between: "justify-between",
  }

  return (
    <div
      className={cn(
        "flex flex-col-reverse gap-2 sm:flex-row sm:items-center",
        alignmentClasses[align],
        className
      )}
    >
      {/* Left side actions (delete/reset) */}
      {(showDelete || showReset) && (
        <div className="flex items-center gap-2">
          {showDelete && onDelete && (
            <Button
              type="button"
              variant="destructive"
              onClick={onDelete}
              disabled={isSaving || isDeleting || isDisabled}
            >
              {isDeleting ? (
                <>
                  <Loader2 className="mr-2 h-4 w-4 animate-spin" />
                  Excluindo...
                </>
              ) : (
                <>
                  <Trash2 className="mr-2 h-4 w-4" />
                  {deleteLabel}
                </>
              )}
            </Button>
          )}
          {showReset && onReset && (
            <Button
              type="button"
              variant="outline"
              onClick={onReset}
              disabled={isSaving || isDeleting || isDisabled}
            >
              <RotateCcw className="mr-2 h-4 w-4" />
              {resetLabel}
            </Button>
          )}
        </div>
      )}

      {/* Spacer for 'between' alignment */}
      {align === "between" && <div className="flex-1" />}

      {/* Custom children */}
      {children}

      {/* Right side actions (cancel/save) */}
      <div className="flex items-center gap-2">
        {showCancel && (
          <Button
            type="button"
            variant="outline"
            onClick={handleCancel}
            disabled={isSaving || isDeleting}
          >
            <X className="mr-2 h-4 w-4" />
            {cancelLabel}
          </Button>
        )}
        {onSave && (
          <Button
            type="submit"
            onClick={onSave}
            disabled={isSaving || isDeleting || isDisabled}
          >
            {isSaving ? (
              <>
                <Loader2 className="mr-2 h-4 w-4 animate-spin" />
                Salvando...
              </>
            ) : (
              <>
                <Save className="mr-2 h-4 w-4" />
                {saveLabel}
              </>
            )}
          </Button>
        )}
      </div>
    </div>
  )
}

// Simplified submit-only button
export interface SubmitButtonProps {
  label?: string
  loadingLabel?: string
  isLoading?: boolean
  isDisabled?: boolean
  icon?: React.ReactNode
  className?: string
}

export function SubmitButton({
  label = "Salvar",
  loadingLabel = "Salvando...",
  isLoading = false,
  isDisabled = false,
  icon,
  className,
}: SubmitButtonProps) {
  return (
    <Button
      type="submit"
      disabled={isLoading || isDisabled}
      className={className}
    >
      {isLoading ? (
        <>
          <Loader2 className="mr-2 h-4 w-4 animate-spin" />
          {loadingLabel}
        </>
      ) : (
        <>
          {icon || <Save className="mr-2 h-4 w-4" />}
          {label}
        </>
      )}
    </Button>
  )
}

// Dialog form actions
export interface DialogFormActionsProps {
  onCancel: () => void
  onConfirm?: () => void | Promise<void>
  cancelLabel?: string
  confirmLabel?: string
  isLoading?: boolean
  isDisabled?: boolean
  confirmVariant?: "default" | "destructive" | "outline" | "secondary" | "ghost" | "link"
  className?: string
}

export function DialogFormActions({
  onCancel,
  onConfirm,
  cancelLabel = "Cancelar",
  confirmLabel = "Confirmar",
  isLoading = false,
  isDisabled = false,
  confirmVariant = "default",
  className,
}: DialogFormActionsProps) {
  return (
    <div className={cn("flex justify-end gap-2", className)}>
      <Button
        type="button"
        variant="outline"
        onClick={onCancel}
        disabled={isLoading}
      >
        {cancelLabel}
      </Button>
      {onConfirm && (
        <Button
          type="submit"
          variant={confirmVariant}
          onClick={onConfirm}
          disabled={isLoading || isDisabled}
        >
          {isLoading ? (
            <>
              <Loader2 className="mr-2 h-4 w-4 animate-spin" />
              Processando...
            </>
          ) : (
            <>
              <Check className="mr-2 h-4 w-4" />
              {confirmLabel}
            </>
          )}
        </Button>
      )}
    </div>
  )
}

// Inline form actions (for inline editing)
export interface InlineFormActionsProps {
  onSave: () => void
  onCancel: () => void
  isLoading?: boolean
  className?: string
}

export function InlineFormActions({
  onSave,
  onCancel,
  isLoading = false,
  className,
}: InlineFormActionsProps) {
  return (
    <div className={cn("flex items-center gap-1", className)}>
      <Button
        type="button"
        variant="ghost"
        size="icon"
        onClick={onCancel}
        disabled={isLoading}
        className="h-8 w-8"
      >
        <X className="h-4 w-4" />
      </Button>
      <Button
        type="button"
        variant="ghost"
        size="icon"
        onClick={onSave}
        disabled={isLoading}
        className="h-8 w-8 text-green-600 hover:text-green-700"
      >
        {isLoading ? (
          <Loader2 className="h-4 w-4 animate-spin" />
        ) : (
          <Check className="h-4 w-4" />
        )}
      </Button>
    </div>
  )
}

// Sticky form actions (fixed to bottom)
export interface StickyFormActionsProps extends FormActionsProps {
  containerClassName?: string
}

export function StickyFormActions({
  containerClassName,
  ...props
}: StickyFormActionsProps) {
  return (
    <div
      className={cn(
        "sticky bottom-0 -mx-6 -mb-6 mt-6 border-t bg-background/95 px-6 py-4 backdrop-blur supports-[backdrop-filter]:bg-background/60",
        containerClassName
      )}
    >
      <FormActions {...props} />
    </div>
  )
}

export default FormActions
