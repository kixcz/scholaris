import AppLayout from '@/layouts/app-layout';
import { Head, router } from '@inertiajs/react';
import { Notebook, Plus, BookOpen, Edit, Trash2 } from 'lucide-react';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Badge } from '@/components/ui/badge';
import { useState, FormEvent } from 'react';

const breadcrumbs = [{ title: 'Dashboard', href: '/dashboard' }, { title: 'Notebooks', href: '/notebooks' }];

interface Notebook {
    id: number;
    name: string;
    description: string | null;
    color: string;
    notes_count: number;
    created_at: string;
}

interface Props {
    notebooks: Notebook[];
}

export default function Notebooks({ notebooks }: Props) {
    const [isOpen, setIsOpen] = useState(false);
    
    const handleSubmit = (e: FormEvent<HTMLFormElement>) => {
        e.preventDefault();
        const formData = new FormData(e.currentTarget);
        
        router.post('/notebooks', {
            name: formData.get('title'),
            description: formData.get('description'),
            color: formData.get('color'),
        }, {
            onSuccess: () => {
                setIsOpen(false);
                // Reset form
                (e.target as HTMLFormElement).reset();
            },
        });
    };
    
    const deleteNotebook = (id: number) => {
        if (confirm('Are you sure you want to delete this notebook?')) {
            router.delete(`/notebooks/${id}`);
        }
    };
    
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Notebooks" />
            <div className="flex flex-1 flex-col gap-4 p-4">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-3xl font-bold tracking-tight">Notebooks</h1>
                        <p className="text-muted-foreground">Organize your research notes</p>
                    </div>
                    <Button onClick={() => setIsOpen(true)}>
                        <Plus className="mr-2 h-4 w-4" />
                        Create Notebook
                    </Button>
                </div>
                
                <Dialog open={isOpen} onOpenChange={setIsOpen}>
                    <DialogContent className="max-w-2xl">
                        <DialogHeader>
                            <DialogTitle>Create Notebook</DialogTitle>
                            <DialogDescription>
                                Create a new notebook to organize your research notes
                            </DialogDescription>
                        </DialogHeader>
                        <form onSubmit={handleSubmit}>
                            <div className="grid gap-4 py-4">
                                <div className="grid gap-2">
                                    <Label htmlFor="title">Notebook Title *</Label>
                                    <Input id="title" name="title" placeholder="e.g., Literature Review Notes" required />
                                </div>
                                <div className="grid gap-2">
                                    <Label htmlFor="description">Description</Label>
                                    <Textarea id="description" name="description" placeholder="What will this notebook contain?" />
                                </div>
                                <div className="grid gap-2">
                                    <Label htmlFor="color">Color</Label>
                                    <Input id="color" name="color" type="color" defaultValue="#3b82f6" className="w-20" />
                                </div>
                            </div>
                            <DialogFooter>
                                <Button type="button" variant="outline" onClick={() => setIsOpen(false)}>
                                    Cancel
                                </Button>
                                <Button type="submit">Create Notebook</Button>
                            </DialogFooter>
                        </form>
                    </DialogContent>
                </Dialog>

                <div className="grid gap-4">
                    {notebooks.length === 0 ? (
                        <Card>
                            <CardContent className="flex flex-col items-center justify-center py-12 text-center">
                                <BookOpen className="h-16 w-16 text-muted-foreground mb-4" />
                                <h3 className="text-lg font-semibold">No notebooks yet</h3>
                                <p className="text-sm text-muted-foreground mb-4">
                                    Create notebooks to organize your research notes
                                </p>
                                <Button onClick={() => setIsOpen(true)}>
                                    <Plus className="mr-2 h-4 w-4" />
                                    Create Your First Notebook
                                </Button>
                            </CardContent>
                        </Card>
                    ) : (
                        <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                            {notebooks.map((notebook) => (
                                <Card 
                                    key={notebook.id} 
                                    className="relative overflow-hidden hover:shadow-md transition-shadow"
                                    style={{ borderLeft: `4px solid ${notebook.color}` }}
                                >
                                    <CardHeader>
                                        <div className="flex items-start justify-between">
                                            <CardTitle className="text-lg">{notebook.name}</CardTitle>
                                            <div className="flex gap-1">
                                                <Button variant="ghost" size="icon" className="h-8 w-8">
                                                    <Edit className="h-4 w-4" />
                                                </Button>
                                                <Button 
                                                    variant="ghost" 
                                                    size="icon" 
                                                    className="h-8 w-8 text-destructive"
                                                    onClick={() => deleteNotebook(notebook.id)}
                                                >
                                                    <Trash2 className="h-4 w-4" />
                                                </Button>
                                            </div>
                                        </div>
                                        {notebook.description && (
                                            <CardDescription className="line-clamp-2">
                                                {notebook.description}
                                            </CardDescription>
                                        )}
                                    </CardHeader>
                                    <CardContent>
                                        <div className="flex items-center justify-between">
                                            <Badge variant="secondary">
                                                {notebook.notes_count} {notebook.notes_count === 1 ? 'note' : 'notes'}
                                            </Badge>
                                            <span className="text-xs text-muted-foreground">
                                                {new Date(notebook.created_at).toLocaleDateString()}
                                            </span>
                                        </div>
                                    </CardContent>
                                </Card>
                            ))}
                        </div>
                    )}
                </div>
            </div>
        </AppLayout>
    );
}
