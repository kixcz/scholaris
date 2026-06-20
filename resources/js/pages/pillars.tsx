import AppLayout from '@/layouts/app-layout';
import { Head, router } from '@inertiajs/react';
import { Pill as PillarIcon, Plus, ChevronDown, ChevronRight, Brain, BarChart3, Microscope, Code, Shield } from 'lucide-react';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Badge } from '@/components/ui/badge';
import { useState } from 'react';

const breadcrumbs = [{ title: 'Dashboard', href: '/dashboard' }, { title: 'Pillars', href: '/pillars' }];

const iconMap: Record<string, any> = {
    Brain,
    BarChart3,
    Microscope,
    Code,
    Shield,
};

interface Topic {
    id: number;
    name: string;
    description: string | null;
}

interface Domain {
    id: number;
    name: string;
    description: string | null;
    color: string;
    topics: Topic[];
}

interface Pillar {
    id: number;
    name: string;
    description: string | null;
    icon: string | null;
    color: string;
    sort_order: number;
    is_active: boolean;
    references_count: number;
    vocabulary_count: number;
    links_count: number;
    domains: Domain[];
}

interface Props {
    pillars: Pillar[];
}

export default function Pillars({ pillars }: Props) {
    const [isOpen, setIsOpen] = useState(false);
    const [expandedPillars, setExpandedPillars] = useState<Set<number>>(new Set());
    const [expandedDomains, setExpandedDomains] = useState<Set<number>>(new Set());

    const togglePillar = (pillarId: number) => {
        const newExpanded = new Set(expandedPillars);
        if (newExpanded.has(pillarId)) {
            newExpanded.delete(pillarId);
        } else {
            newExpanded.add(pillarId);
        }
        setExpandedPillars(newExpanded);
    };

    const toggleDomain = (domainId: number) => {
        const newExpanded = new Set(expandedDomains);
        if (newExpanded.has(domainId)) {
            newExpanded.delete(domainId);
        } else {
            newExpanded.add(domainId);
        }
        setExpandedDomains(newExpanded);
    };

    const handleSubmit = (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();
        const formData = new FormData(e.currentTarget);
        
        router.post('/pillars', {
            name: formData.get('name'),
            description: formData.get('description'),
            icon: formData.get('icon'),
            color: formData.get('color'),
            sort_order: formData.get('sort_order'),
        }, {
            onSuccess: () => setIsOpen(false),
        });
    };

    const totalResources = pillars.reduce(
        (sum, p) => sum + p.references_count + p.vocabulary_count + p.links_count,
        0
    );

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Pillars" />
            <div className="flex flex-1 flex-col gap-4 p-4">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-3xl font-bold tracking-tight">Knowledge Pillars</h1>
                        <p className="text-muted-foreground">
                            Organize your expertise across {pillars.length} pillars with {totalResources} resources
                        </p>
                    </div>
                    <Button onClick={() => setIsOpen(true)}>
                        <Plus className="mr-2 h-4 w-4" />
                        Create Pillar
                    </Button>
                </div>

                <Dialog open={isOpen} onOpenChange={setIsOpen}>
                    <DialogContent className="max-w-2xl">
                        <DialogHeader>
                            <DialogTitle>Create Knowledge Pillar</DialogTitle>
                            <DialogDescription>
                                Define a major area of expertise or research interest
                            </DialogDescription>
                        </DialogHeader>
                        <form onSubmit={handleSubmit}>
                            <div className="grid gap-4 py-4">
                                <div className="grid gap-2">
                                    <Label htmlFor="name">Pillar Name *</Label>
                                    <Input 
                                        id="name" 
                                        name="name"
                                        placeholder="e.g., Artificial Intelligence" 
                                        required 
                                    />
                                </div>
                                <div className="grid gap-2">
                                    <Label htmlFor="description">Description</Label>
                                    <Textarea 
                                        id="description" 
                                        name="description"
                                        placeholder="What does this pillar cover?" 
                                    />
                                </div>
                                <div className="grid grid-cols-3 gap-4">
                                    <div className="grid gap-2">
                                        <Label htmlFor="icon">Icon</Label>
                                        <Input 
                                            id="icon" 
                                            name="icon"
                                            placeholder="Brain" 
                                        />
                                        <p className="text-xs text-muted-foreground">
                                            Brain, BarChart3, Microscope, Code, Shield
                                        </p>
                                    </div>
                                    <div className="grid gap-2">
                                        <Label htmlFor="color">Color</Label>
                                        <Input 
                                            id="color" 
                                            name="color"
                                            type="color" 
                                            defaultValue="#3498db" 
                                        />
                                    </div>
                                    <div className="grid gap-2">
                                        <Label htmlFor="sort_order">Sort Order</Label>
                                        <Input 
                                            id="sort_order" 
                                            name="sort_order"
                                            type="number" 
                                            defaultValue="0" 
                                        />
                                    </div>
                                </div>
                            </div>
                            <DialogFooter>
                                <Button type="button" variant="outline" onClick={() => setIsOpen(false)}>
                                    Cancel
                                </Button>
                                <Button type="submit">Create Pillar</Button>
                            </DialogFooter>
                        </form>
                    </DialogContent>
                </Dialog>

                <div className="grid gap-4">
                    {pillars.length === 0 ? (
                        <Card>
                            <CardContent className="flex flex-col items-center justify-center py-12 text-center">
                                <PillarIcon className="h-16 w-16 text-muted-foreground mb-4" />
                                <h3 className="text-lg font-semibold">No pillars yet</h3>
                                <p className="text-sm text-muted-foreground mb-4">
                                    Create your first knowledge pillar to organize your learning
                                </p>
                                <Button onClick={() => setIsOpen(true)}>
                                    <Plus className="mr-2 h-4 w-4" />
                                    Create Your First Pillar
                                </Button>
                            </CardContent>
                        </Card>
                    ) : (
                        pillars.map((pillar) => {
                            const Icon = iconMap[pillar.icon || 'Brain'] || Brain;
                            const isExpanded = expandedPillars.has(pillar.id);
                            const resourceCount = pillar.references_count + pillar.vocabulary_count + pillar.links_count;

                            return (
                                <Card key={pillar.id} className="overflow-hidden">
                                    <CardHeader 
                                        className="cursor-pointer hover:bg-muted/50 transition-colors"
                                        onClick={() => togglePillar(pillar.id)}
                                        style={{ borderLeft: `4px solid ${pillar.color}` }}
                                    >
                                        <div className="flex items-start justify-between">
                                            <div className="flex items-center gap-3">
                                                <div 
                                                    className="flex h-10 w-10 items-center justify-center rounded-lg"
                                                    style={{ backgroundColor: `${pillar.color}20` }}
                                                >
                                                    <Icon className="h-5 w-5" style={{ color: pillar.color }} />
                                                </div>
                                                <div>
                                                    <CardTitle className="text-xl">{pillar.name}</CardTitle>
                                                    {pillar.description && (
                                                        <CardDescription className="mt-1">
                                                            {pillar.description}
                                                        </CardDescription>
                                                    )}
                                                </div>
                                            </div>
                                            <div className="flex items-center gap-2">
                                                <Badge variant="secondary">
                                                    {pillar.domains.length} domains
                                                </Badge>
                                                <Badge variant="outline">
                                                    {resourceCount} resources
                                                </Badge>
                                                {isExpanded ? (
                                                    <ChevronDown className="h-5 w-5 text-muted-foreground" />
                                                ) : (
                                                    <ChevronRight className="h-5 w-5 text-muted-foreground" />
                                                )}
                                            </div>
                                        </div>
                                    </CardHeader>

                                    {isExpanded && (
                                        <CardContent className="pt-4">
                                            <div className="space-y-4">
                                                {pillar.domains.map((domain) => {
                                                    const domainExpanded = expandedDomains.has(domain.id);
                                                    return (
                                                        <div key={domain.id} className="rounded-lg border">
                                                            <div
                                                                className="flex items-center justify-between p-3 cursor-pointer hover:bg-muted/50 transition-colors"
                                                                onClick={() => toggleDomain(domain.id)}
                                                                style={{ borderLeft: `3px solid ${domain.color}` }}
                                                            >
                                                                <div className="flex items-center gap-2">
                                                                    <div 
                                                                        className="h-3 w-3 rounded-full"
                                                                        style={{ backgroundColor: domain.color }}
                                                                    />
                                                                    <span className="font-medium">{domain.name}</span>
                                                                    <Badge variant="secondary" className="text-xs">
                                                                        {domain.topics.length} topics
                                                                    </Badge>
                                                                </div>
                                                                {domainExpanded ? (
                                                                    <ChevronDown className="h-4 w-4" />
                                                                ) : (
                                                                    <ChevronRight className="h-4 w-4" />
                                                                )}
                                                            </div>

                                                            {domainExpanded && (
                                                                <div className="px-3 pb-3 pl-8">
                                                                    <div className="flex flex-wrap gap-2">
                                                                        {domain.topics.map((topic) => (
                                                                            <Badge 
                                                                                key={topic.id} 
                                                                                variant="outline"
                                                                                className="text-xs"
                                                                            >
                                                                                {topic.name}
                                                                            </Badge>
                                                                        ))}
                                                                    </div>
                                                                </div>
                                                            )}
                                                        </div>
                                                    );
                                                })}
                                            </div>
                                        </CardContent>
                                    )}
                                </Card>
                            );
                        })
                    )}
                </div>
            </div>
        </AppLayout>
    );
}
