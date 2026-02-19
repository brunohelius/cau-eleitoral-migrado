import { useState } from 'react';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { useToast } from '@/components/ui/use-toast';
import { api } from '@/lib/api';
import { Plus, Pencil, Trash2, Users, CheckCircle, XCircle } from 'lucide-react';

interface ComissaoEleitoral {
  id: string;
  nome: string;
  descricao?: string;
  sigla?: string;
  portaria?: string;
  dataPortaria?: string;
  dataInicio?: string;
  dataFim?: string;
  ativa: boolean;
  motivoEncerramento?: string;
  calendarioNome?: string;
  filialNome?: string;
  totalMembros: number;
  membrosAtivos: number;
}

export default function ComissoesPage() {
  const { toast } = useToast();
  const queryClient = useQueryClient();
  const [isDialogOpen, setIsDialogOpen] = useState(false);
  const [formData, setFormData] = useState({
    nome: '',
    descricao: '',
    sigla: '',
    portaria: '',
  });

  const { data: comissoes, isLoading } = useQuery({
    queryKey: ['comissoes'],
    queryFn: async () => {
      const response = await api.get('/comissao');
      return response.data as { items: ComissaoEleitoral[] };
    },
  });

  const createMutation = useMutation({
    mutationFn: async (data: typeof formData) => {
      const response = await api.post('/comissao', data);
      return response.data;
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['comissoes'] });
      setIsDialogOpen(false);
      setFormData({ nome: '', descricao: '', sigla: '', portaria: '' });
      toast({ title: 'Sucesso', description: 'Comissão criada com sucesso' });
    },
    onError: () => {
      toast({ title: 'Erro', description: 'Falha ao criar comissão', variant: 'destructive' });
    },
  });

  const deleteMutation = useMutation({
    mutationFn: async (id: string) => {
      await api.delete(`/comissao/${id}`);
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['comissoes'] });
      toast({ title: 'Sucesso', description: 'Comissão excluída com sucesso' });
    },
    onError: () => {
      toast({ title: 'Erro', description: 'Falha ao excluir comissão', variant: 'destructive' });
    },
  });

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    createMutation.mutate(formData);
  };

  const getStatusBadge = (ativa: boolean) => {
    return ativa ? (
      <Badge className="bg-green-500"><CheckCircle className="w-3 h-3 mr-1" />Ativa</Badge>
    ) : (
      <Badge variant="secondary"><XCircle className="w-3 h-3 mr-1" />Inativa</Badge>
    );
  };

  return (
    <div className="container mx-auto py-6">
      <div className="flex justify-between items-center mb-6">
        <div>
          <h1 className="text-3xl font-bold">Comissões Eleitorais</h1>
          <p className="text-muted-foreground">Gerencie as comissões eleitorais e seus membros</p>
        </div>
        <Dialog open={isDialogOpen} onOpenChange={setIsDialogOpen}>
          <DialogTrigger asChild>
            <Button onClick={() => setFormData({ nome: '', descricao: '', sigla: '', portaria: '' })}>
              <Plus className="w-4 h-4 mr-2" />Nova Comissão
            </Button>
          </DialogTrigger>
          <DialogContent>
            <DialogHeader>
              <DialogTitle>Nova Comissão Eleitoral</DialogTitle>
              <DialogDescription>Preencha os dados para criar uma nova comissão</DialogDescription>
            </DialogHeader>
            <form onSubmit={handleSubmit} className="space-y-4">
              <div>
                <Label htmlFor="nome">Nome *</Label>
                <Input
                  id="nome"
                  value={formData.nome}
                  onChange={(e) => setFormData({ ...formData, nome: e.target.value })}
                  required
                />
              </div>
              <div>
                <Label htmlFor="sigla">Sigla</Label>
                <Input
                  id="sigla"
                  value={formData.sigla}
                  onChange={(e) => setFormData({ ...formData, sigla: e.target.value })}
                />
              </div>
              <div>
                <Label htmlFor="descricao">Descrição</Label>
                <Input
                  id="descricao"
                  value={formData.descricao}
                  onChange={(e) => setFormData({ ...formData, descricao: e.target.value })}
                />
              </div>
              <div>
                <Label htmlFor="portaria">Portaria</Label>
                <Input
                  id="portaria"
                  value={formData.portaria}
                  onChange={(e) => setFormData({ ...formData, portaria: e.target.value })}
                />
              </div>
              <div className="flex justify-end gap-2">
                <Button type="button" variant="outline" onClick={() => setIsDialogOpen(false)}>
                  Cancelar
                </Button>
                <Button type="submit" disabled={createMutation.isPending}>
                  {createMutation.isPending ? 'Salvando...' : 'Salvar'}
                </Button>
              </div>
            </form>
          </DialogContent>
        </Dialog>
      </div>

      <Card>
        <CardHeader>
          <CardTitle>Lista de Comissões</CardTitle>
          <CardDescription>Todas as comissões eleitorais cadastradas</CardDescription>
        </CardHeader>
        <CardContent>
          {isLoading ? (
            <div className="text-center py-8">Carregando...</div>
          ) : (
            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead>Nome</TableHead>
                  <TableHead>Sigla</TableHead>
                  <TableHead>Portaria</TableHead>
                  <TableHead>Período</TableHead>
                  <TableHead>Membros</TableHead>
                  <TableHead>Status</TableHead>
                  <TableHead className="text-right">Ações</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                {comissoes?.items?.map((comissao) => (
                  <TableRow key={comissao.id}>
                    <TableCell className="font-medium">{comissao.nome}</TableCell>
                    <TableCell>{comissao.sigla || '-'}</TableCell>
                    <TableCell>{comissao.portaria || '-'}</TableCell>
                    <TableCell>
                      {comissao.dataInicio && comissao.dataFim ? (
                        <span className="text-sm">
                          {new Date(comissao.dataInicio).toLocaleDateString('pt-BR')} - 
                          {new Date(comissao.dataFim).toLocaleDateString('pt-BR')}
                        </span>
                      ) : (
                        '-'
                      )}
                    </TableCell>
                    <TableCell>
                      <div className="flex items-center gap-1">
                        <Users className="w-4 h-4" />
                        {comissao.membrosAtivos}/{comissao.totalMembros}
                      </div>
                    </TableCell>
                    <TableCell>{getStatusBadge(comissao.ativa)}</TableCell>
                    <TableCell className="text-right">
                      <div className="flex justify-end gap-2">
                        <Button
                          variant="ghost"
                          size="icon"
                          onClick={() => {
                            if (confirm('Tem certeza que deseja excluir esta comissão?')) {
                              deleteMutation.mutate(comissao.id);
                            }
                          }}
                        >
                          <Trash2 className="w-4 h-4 text-red-500" />
                        </Button>
                      </div>
                    </TableCell>
                  </TableRow>
                ))}
                {(!comissoes?.items || comissoes.items.length === 0) && (
                  <TableRow>
                    <TableCell colSpan={7} className="text-center py-8 text-muted-foreground">
                      Nenhuma comissão encontrada
                    </TableCell>
                  </TableRow>
                )}
              </TableBody>
            </Table>
          )}
        </CardContent>
      </Card>
    </div>
  );
}
