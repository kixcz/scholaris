import AppLayout from '@/layouts/app-layout';
import { Head } from '@inertiajs/react';
import { Target, Plus, Calendar, BookOpen, FileText, Brain, StickyNote, Video, Clock } from 'lucide-react';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { useState } from 'react';

const breadcrumbs = [{ title: 'Dashboard', href: '/dashboard' }, { title: 'Weekly Goals', href: '/weekly-goals' }];

export default function WeeklyGoals() {
    const [isOpen, setIsOpen] = useState(false);

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Weekly Goals" />
            <div className="flex flex-1 flex-col gap-4 p-4">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-3xl font-bold tracking-tight">Weekly Goals</h1>
                        <p className="text-muted-foreground">Set and track weekly targets</p>
                    </div>
                    <Button onClick={() => setIsOpen(true)}>
                        <Plus className="mr-2 h-4 w-4" />
                        Create Weekly Goal
                    </Button>
                </div>

                <Dialog open={isOpen} onOpenChange={setIsOpen}>
                    <DialogContent className="max-w-3xl max-h-[90vh] overflow-y-auto">
                        <DialogHeader>
                            <DialogTitle>Create Weekly Goal</DialogTitle>
                            <DialogDescription>
                                Define your targets for this week across all modules
                            </DialogDescription>
                        </DialogHeader>
                        <div className="grid gap-4 py-4">
                            <div className="grid gap-2">
                                <Label htmlFor="title">Goal Title *</Label>
                                <Input id="title" placeholder="e.g., Research Reading Sprint - Week 12" />
                            </div>
                            <div className="grid grid-cols-2 gap-4">
                                <div className="grid gap-2">
                                    <Label htmlFor="week_start">Week Start</Label>
                                    <Input id="week_start" type="date" />
                                </div>
                                <div className="grid gap-2">
                                    <Label htmlFor="week_end">Week End</Label>
                                    <Input id="week_end" type="date" />
                                </div>
                            </div>
                            <div className="grid gap-2">
                                <Label htmlFor="linked_roadmap">Linked Roadmap</Label>
                                <Select>
                                    <SelectTrigger>
                                        <SelectValue placeholder="Select roadmap (optional)" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="1">Publish Q2 Journal Paper</SelectItem>
                                        <SelectItem value="2">PhD Thesis Completion</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                            <div className="grid gap-2">
                                <Label htmlFor="linked_milestone">Linked Milestone</Label>
                                <Select>
                                    <SelectTrigger>
                                        <SelectValue placeholder="Select milestone (optional)" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="1">Literature Review</SelectItem>
                                        <SelectItem value="2">Data Analysis</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                            <div className="rounded-lg border p-4">
                                <Label className="mb-3 block">Weekly Targets</Label>
                                <div className="grid grid-cols-2 gap-4">
                                    <div className="grid gap-2">
                                        <Label htmlFor="papers" className="flex items-center gap-2">
                                            <BookOpen className="h-4 w-4" />
                                            Papers to Read
                                        </Label>
                                        <Input id="papers" type="number" placeholder="10" />
                                    </div>
                                    <div className="grid gap-2">
                                        <Label htmlFor="summaries" className="flex items-center gap-2">
                                            <FileText className="h-4 w-4" />
                                            Summaries to Write
                                        </Label>
                                        <Input id="summaries" type="number" placeholder="5" />
                                    </div>
                                    <div className="grid gap-2">
                                        <Label htmlFor="vocabulary" className="flex items-center gap-2">
                                            <Brain className="h-4 w-4" />
                                            Vocabulary Terms
                                        </Label>
                                        <Input id="vocabulary" type="number" placeholder="20" />
                                    </div>
                                    <div className="grid gap-2">
                                        <Label htmlFor="notes">Research Notes</Label>
                                        <Input id="notes" type="number" placeholder="3" />
                                    </div>
                                    <div className="grid gap-2">
                                        <Label htmlFor="tutorials">Tutorials to Complete</Label>
                                        <Input id="tutorials" type="number" placeholder="2" />
                                    </div>
                                    <div className="grid gap-2">
                                        <Label htmlFor="study_hours">Study Hours</Label>
                                        <Input id="study_hours" type="number" placeholder="15" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <DialogFooter>
                            <Button variant="outline" onClick={() => setIsOpen(false)}>
                                Cancel
                            </Button>
                            <Button onClick={() => setIsOpen(false)}>
                                Create Goal
                            </Button>
                        </DialogFooter>
                    </DialogContent>
                </Dialog>

                <Card>
                    <CardHeader>
                        <CardTitle>This Week's Goals</CardTitle>
                        <CardDescription>No goals set for this week</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div className="flex flex-col items-center justify-center py-12 text-center">
                            <Calendar className="h-16 w-16 text-muted-foreground mb-4" />
                            <h3 className="text-lg font-semibold">No goals yet</h3>
                            <p className="text-sm text-muted-foreground mb-4">
                                Set targets to stay productive this week
                            </p>
                            <Button onClick={() => setIsOpen(true)}>
                                <Plus className="mr-2 h-4 w-4" />
                                Set Weekly Goals
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}
