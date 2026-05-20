<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TherapistNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'therapist_id',
        'content',
        'type',
        'author_id',
        'is_private',
        'is_important',
        'follow_up_required',
        'follow_up_date',
        'resolved_at',
        'resolved_by',
        'tags',
        'attachments',
    ];

    protected $casts = [
        'is_private' => 'boolean',
        'is_important' => 'boolean',
        'follow_up_required' => 'boolean',
        'follow_up_date' => 'date',
        'resolved_at' => 'datetime',
        'tags' => 'array',
        'attachments' => 'array',
    ];

    // Relationships
    public function therapist(): BelongsTo
    {
        return $this->belongsTo(Therapist::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function resolvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    // Scopes
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopePrivate($query)
    {
        return $query->where('is_private', true);
    }

    public function scopePublic($query)
    {
        return $query->where('is_private', false);
    }

    public function scopeImportant($query)
    {
        return $query->where('is_important', true);
    }

    public function scopeRequiresFollowUp($query)
    {
        return $query->where('follow_up_required', true);
    }

    public function scopeOverdueFollowUp($query)
    {
        return $query->where('follow_up_required', true)
            ->where('follow_up_date', '<', now())
            ->whereNull('resolved_at');
    }

    public function scopeResolved($query)
    {
        return $query->whereNotNull('resolved_at');
    }

    public function scopeUnresolved($query)
    {
        return $query->whereNull('resolved_at');
    }

    public function scopeByAuthor($query, $authorId)
    {
        return $query->where('author_id', $authorId);
    }

    public function scopeByTherapist($query, $therapistId)
    {
        return $query->where('therapist_id', $therapistId);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // Accessors
    public function getTypeLabelAttribute()
    {
        return ucfirst(str_replace('_', ' ', $this->type));
    }

    public function getAuthorNameAttribute()
    {
        return $this->author?->name;
    }

    public function getIsResolvedAttribute()
    {
        return !is_null($this->resolved_at);
    }

    public function getIsOverdueAttribute()
    {
        return $this->follow_up_required && 
               $this->follow_up_date && 
               $this->follow_up_date->isPast() && 
               !$this->is_resolved;
    }

    public function getFormattedFollowUpDateAttribute()
    {
        return $this->follow_up_date ? $this->follow_up_date->format('Y-m-d') : null;
    }

    // Helper methods
    public function resolve($resolvedBy): void
    {
        $this->update([
            'resolved_at' => now(),
            'resolved_by' => $resolvedBy,
        ]);
    }

    public function reopen(): void
    {
        $this->update([
            'resolved_at' => null,
            'resolved_by' => null,
        ]);
    }

    public function markAsImportant(): void
    {
        $this->update(['is_important' => true]);
    }

    public function markAsNormal(): void
    {
        $this->update(['is_important' => false]);
    }

    public function makePrivate(): void
    {
        $this->update(['is_private' => true]);
    }

    public function makePublic(): void
    {
        $this->update(['is_private' => false]);
    }

    public function requireFollowUp($followUpDate = null): void
    {
        $this->update([
            'follow_up_required' => true,
            'follow_up_date' => $followUpDate ?? now()->addWeek(),
        ]);
    }

    public function removeFollowUp(): void
    {
        $this->update([
            'follow_up_required' => false,
            'follow_up_date' => null,
        ]);
    }

    public function addTag($tag): void
    {
        $tags = $this->tags ?? [];
        if (!in_array($tag, $tags)) {
            $tags[] = $tag;
            $this->update(['tags' => $tags]);
        }
    }

    public function removeTag($tag): void
    {
        $tags = $this->tags ?? [];
        $key = array_search($tag, $tags);
        if ($key !== false) {
            unset($tags[$key]);
            $this->update(['tags' => array_values($tags)]);
        }
    }

    public function addAttachment($attachment): void
    {
        $attachments = $this->attachments ?? [];
        $attachments[] = $attachment;
        $this->update(['attachments' => $attachments]);
    }

    public function removeAttachment($index): void
    {
        $attachments = $this->attachments ?? [];
        if (isset($attachments[$index])) {
            unset($attachments[$index]);
            $this->update(['attachments' => array_values($attachments)]);
        }
    }

    public static function getTypes(): array
    {
        return [
            'general' => 'General',
            'performance' => 'Performance',
            'attendance' => 'Attendance',
            'complaint' => 'Complaint',
            'compliment' => 'Compliment',
            'disciplinary' => 'Disciplinary',
            'training' => 'Training',
            'medical' => 'Medical',
            'personal' => 'Personal',
            'safety' => 'Safety',
            'hr' => 'HR',
        ];
    }

    public function getSummary(): array
    {
        return [
            'id' => $this->id,
            'content' => $this->content,
            'type' => $this->type_label,
            'author_name' => $this->author_name,
            'is_private' => $this->is_private,
            'is_important' => $this->is_important,
            'is_resolved' => $this->is_resolved,
            'is_overdue' => $this->is_overdue,
            'follow_up_required' => $this->follow_up_required,
            'follow_up_date' => $this->formatted_follow_up_date,
            'resolved_at' => $this->resolved_at?->format('Y-m-d H:i'),
            'resolved_by' => $this->resolvedBy?->name,
            'tags' => $this->tags,
            'attachment_count' => count($this->attachments ?? []),
            'created_at' => $this->created_at->format('Y-m-d H:i'),
            'therapist_id' => $this->therapist_id,
        ];
    }

    public static function getNotesSummary($therapistId): array
    {
        $notes = self::where('therapist_id', $therapistId)->get();
        
        return [
            'total_notes' => $notes->count(),
            'private_notes' => $notes->where('is_private', true)->count(),
            'public_notes' => $notes->where('is_private', false)->count(),
            'important_notes' => $notes->where('is_important', true)->count(),
            'resolved_notes' => $notes->whereNotNull('resolved_at')->count(),
            'unresolved_notes' => $notes->whereNull('resolved_at')->count(),
            'follow_up_required' => $notes->where('follow_up_required', true)->count(),
            'overdue_follow_up' => $notes->where('follow_up_required', true)
                ->where('follow_up_date', '<', now())
                ->whereNull('resolved_at')
                ->count(),
            'by_type' => $notes->groupBy('type')->map(function ($typeNotes) {
                return $typeNotes->count();
            }),
            'recent_notes' => $notes->sortByDesc('created_at')
                ->take(5)
                ->map(function ($note) {
                    return [
                        'id' => $note->id,
                        'content' => substr($note->content, 0, 100) . '...',
                        'type' => $note->type_label,
                        'created_at' => $note->created_at->format('Y-m-d H:i'),
                    ];
                })->values(),
        ];
    }
}
