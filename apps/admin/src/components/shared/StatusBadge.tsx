import * as React from "react"
import { cva, type VariantProps } from "class-variance-authority"
import {
  CheckCircle2,
  XCircle,
  Clock,
  AlertCircle,
  Pause,
  Play,
  Archive,
  Ban,
  Loader2,
  Circle
} from "lucide-react"
import { cn } from "@/lib/utils"

const statusBadgeVariants = cva(
  "inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-semibold transition-colors",
  {
    variants: {
      variant: {
        success: "bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400",
        error: "bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400",
        warning: "bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400",
        info: "bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400",
        neutral: "bg-gray-100 text-gray-800 dark:bg-gray-800/50 dark:text-gray-400",
        purple: "bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400",
        pending: "bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400",
      },
      size: {
        sm: "text-[10px] px-2 py-0.5",
        default: "text-xs px-2.5 py-0.5",
        lg: "text-sm px-3 py-1",
      },
    },
    defaultVariants: {
      variant: "neutral",
      size: "default",
    },
  }
)

export type StatusBadgeVariant = NonNullable<VariantProps<typeof statusBadgeVariants>["variant"]>

// Common status mappings
export const STATUS_VARIANTS: Record<string, StatusBadgeVariant> = {
  // Generic statuses
  active: "success",
  inactive: "neutral",
  enabled: "success",
  disabled: "neutral",
  published: "success",
  draft: "neutral",
  archived: "neutral",

  // Process statuses
  pending: "pending",
  processing: "info",
  completed: "success",
  failed: "error",
  cancelled: "neutral",
  rejected: "error",
  approved: "success",

  // Election statuses
  aberta: "success",
  fechada: "neutral",
  em_andamento: "info",
  encerrada: "neutral",
  suspensa: "warning",
  anulada: "error",

  // User/Candidate statuses
  ativo: "success",
  inativo: "neutral",
  bloqueado: "error",
  aguardando: "pending",
  confirmado: "success",

  // Vote statuses
  votou: "success",
  nao_votou: "neutral",
  voto_nulo: "warning",
  voto_branco: "neutral",
}

// Icon mappings
const STATUS_ICONS: Record<string, React.ElementType> = {
  success: CheckCircle2,
  error: XCircle,
  warning: AlertCircle,
  info: Circle,
  neutral: Circle,
  purple: Circle,
  pending: Clock,
}

export interface StatusBadgeProps
  extends Omit<React.HTMLAttributes<HTMLDivElement>, "children">,
    VariantProps<typeof statusBadgeVariants> {
  status?: string
  label?: string
  showIcon?: boolean
  icon?: React.ElementType
  pulse?: boolean
}

export function StatusBadge({
  className,
  variant,
  size,
  status,
  label,
  showIcon = true,
  icon,
  pulse = false,
  ...props
}: StatusBadgeProps) {
  // Resolve variant from status if not provided directly
  const resolvedVariant = variant || (status ? STATUS_VARIANTS[status.toLowerCase().replace(/\s+/g, "_")] : "neutral") || "neutral"

  // Resolve icon
  const Icon = icon || STATUS_ICONS[resolvedVariant] || Circle

  // Resolve label
  const displayLabel = label || status?.replace(/_/g, " ") || "Status"

  return (
    <div
      className={cn(statusBadgeVariants({ variant: resolvedVariant, size }), className)}
      {...props}
    >
      {showIcon && (
        <Icon
          className={cn(
            "h-3 w-3",
            pulse && "animate-pulse"
          )}
        />
      )}
      <span className="capitalize">{displayLabel}</span>
    </div>
  )
}

// Pre-configured status badges for common use cases
export function ActiveBadge({ active, ...props }: { active: boolean } & Omit<StatusBadgeProps, "status" | "variant">) {
  return (
    <StatusBadge
      variant={active ? "success" : "neutral"}
      label={active ? "Ativo" : "Inativo"}
      icon={active ? CheckCircle2 : Ban}
      {...props}
    />
  )
}

export function PublishedBadge({ published, ...props }: { published: boolean } & Omit<StatusBadgeProps, "status" | "variant">) {
  return (
    <StatusBadge
      variant={published ? "success" : "neutral"}
      label={published ? "Publicado" : "Rascunho"}
      icon={published ? CheckCircle2 : Archive}
      {...props}
    />
  )
}

export function ProcessBadge({ status, ...props }: { status: "pending" | "processing" | "completed" | "failed" } & Omit<StatusBadgeProps, "status" | "variant">) {
  const config = {
    pending: { variant: "pending" as const, label: "Pendente", icon: Clock, pulse: false },
    processing: { variant: "info" as const, label: "Processando", icon: Loader2, pulse: true },
    completed: { variant: "success" as const, label: "Concluido", icon: CheckCircle2, pulse: false },
    failed: { variant: "error" as const, label: "Falhou", icon: XCircle, pulse: false },
  }

  const { variant, label, icon, pulse } = config[status]

  return (
    <StatusBadge
      variant={variant}
      label={label}
      icon={icon}
      pulse={pulse}
      {...props}
    />
  )
}

export function ElectionStatusBadge({ status, ...props }: {
  status: "aberta" | "fechada" | "em_andamento" | "encerrada" | "suspensa" | "anulada"
} & Omit<StatusBadgeProps, "status" | "variant">) {
  const config = {
    aberta: { variant: "success" as const, label: "Aberta", icon: Play, pulse: false },
    fechada: { variant: "neutral" as const, label: "Fechada", icon: Pause, pulse: false },
    em_andamento: { variant: "info" as const, label: "Em Andamento", icon: Loader2, pulse: true },
    encerrada: { variant: "neutral" as const, label: "Encerrada", icon: Archive, pulse: false },
    suspensa: { variant: "warning" as const, label: "Suspensa", icon: Pause, pulse: false },
    anulada: { variant: "error" as const, label: "Anulada", icon: Ban, pulse: false },
  }

  const { variant, label, icon, pulse } = config[status] || config.fechada

  return (
    <StatusBadge
      variant={variant}
      label={label}
      icon={icon}
      pulse={pulse}
      {...props}
    />
  )
}

export function VoteBadge({ voted, ...props }: { voted: boolean } & Omit<StatusBadgeProps, "status" | "variant">) {
  return (
    <StatusBadge
      variant={voted ? "success" : "neutral"}
      label={voted ? "Votou" : "Nao Votou"}
      icon={voted ? CheckCircle2 : Circle}
      {...props}
    />
  )
}

export default StatusBadge
