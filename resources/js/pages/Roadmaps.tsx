import AppLayout from '@/layouts/app-layout';
import { Head } from '@inertiajs/react';
import { Map, Plus, Target } from 'lucide-react';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Badge } from '@/components/ui/badge';
import { useState } from 'react';

const breadcrumbs = [{ title: 'Dashboard', href: '/dashboard' }, { title: 'Roadmaps', href: '/roadmaps' }];

export default function Roadmaps() {
    const [isOpen, setIsOpen] = useState(false);

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Roadmaps" />
            <div className="flex flex-1 flex-col gap-4 p-4">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-3xl font-bold tracking-tight">Roadmaps</h1>
                        <p className="text-muted-foreground">Strategic long-term plans</p>
                    </div>
                    <Button onClick={() => setIsOpen(true)}>
                        <Plus className="mr-2 h-4 w-4" />
                        Create Roadmap
                    </Button>
                </div>

                <Dialog open={isOpen} onOpenChange={setIsOpen}>
                    <DialogContent className="max-w-2xl max-h-[90vh] overflow-y-auto">
                        <DialogHeader>
                            <DialogTitle>Create New Roadmap</DialogTitle>
                            <DialogDescription>
                                Define your long-term strategic plan with phases and milestones
                            </DialogDescription>
                        </DialogHeader>
                        <div className="grid gap-4 py-4">
                            <div className="grid gap-2">
                                <Label htmlFor="title">Roadmap Title *</Label>
                                <Input id="title" placeholder="e.g., Publish a Q2 Journal Paper" />
                            </div>
                            <div className="grid gap-2">
                                <Label htmlFor="description">Description</Label>
                                <Textarea id="description" placeholder="Describe your roadmap objectives and expected outcomes..." />
                            </div>
                            <div className="grid grid-cols-2 gap-4">
                                <div className="grid gap-2">
                                    <Label htmlFor="category">Category</Label>
                                    <Select>
                                        <SelectTrigger>
                                            <SelectValue placeholder="Select category" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="academic">Academic</SelectItem>
                                            <SelectItem value="research">Research</SelectItem>
                                            <SelectItem value="career">Career</SelectItem>
                                            <SelectItem value="certification">Certification</SelectItem>
                                            <SelectItem value="skill">Skill Development</SelectItem>
                                        </SelectContent>
                                    </Select>
                                </div>
                                <div className="grid gap-2">
                                    <Label htmlFor="duration">Duration (weeks)</Label>
                                    <Input id="duration" type="number" placeholder="12" />
                                </div>
                            </div>
                            <div className="grid grid-cols-2 gap-4">
                                <div className="grid gap-2">
                                    <Label htmlFor="start_date">Start Date</Label>
                                    <Input id="start_date" type="date" />
                                </div>
                                <div className="grid gap-2">
                                    <Label htmlFor="target_end_date">Target End Date</Label>
                                    <Input id="target_end_date" type="date" />
                                </div>
                            </div>
                            <div className="grid gap-2">
                                <Label htmlFor="success_criteria">Success Criteria</Label>
                                <Textarea id="success_criteria" placeholder="Define how you'll measure completion..." />
                            </div>
                            <div className="grid gap-2">
                                <Label htmlFor="phases">Phases & Milestones</Label>
                                <Textarea 
                                    id="phases" 
                                    placeholder="Phase 1: Literature Review&#10;Phase 2: Data Collection&#10;Phase 3: Analysis&#10;Phase 4: Writing" 
                                    className="min-h-[120px]" 
                                />
                            </div>
                            <div className="grid gap-2">
                                <Label htmlFor="linked_modules">Linked Modules</Label>
                                <div className="flex gap-2 flex-wrap">
                                    <Button variant="outline" size="sm">References</Button>
                                    <Button variant="outline" size="sm">Notebooks</Button>
                                    <Button variant="outline" size="sm">Vocabulary</Button>
                                    <Button variant="outline" size="sm">Tutorials</Button>
                                    <Button variant="outline" size="sm">Tasks</Button>
                                </div>
                            </div>
                        </div>
                        <DialogFooter>
                            <Button variant="outline" onClick={() => setIsOpen(false)}>
                                Cancel
                            </Button>
                            <Button onClick={() => setIsOpen(false)}>
                                Create Roadmap
                            </Button>
                        </DialogFooter>
                    </DialogContent>
                </Dialog>

                <Card>
                    <CardHeader>
                        <CardTitle>Your Roadmaps</CardTitle>
                        <CardDescription>0 active roadmaps</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div className="flex flex-col items-center justify-center py-12 text-center">
                            <Map className="h-16 w-16 text-muted-foreground mb-4" />
                            <h3 className="text-lg font-semibold">No roadmaps yet</h3>
                            <p className="text-sm text-muted-foreground mb-4">
                                Create your first strategic plan to guide your research journey
                            </p>
                            <Button onClick={() => setIsOpen(true)}>
                                <Plus className="mr-2 h-4 w-4" />
                                Create Your First Roadmap
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}
